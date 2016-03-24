<?php


//A: Unless this is named differently, I could not find any instance of ToolType
//   on SafetySign

// by product

class ToolType extends CacheableEntity {

	/**
	 *
	 */
	const FILEMAKER_LAYOUT_PATH = '';

	/**
	 * Constant used for two purposes
	 *
	 * - Getting the record from the database
	 * - $FULL_TABLE_DUMP is a keyword used for our cache system so it can know what query to run
	 */
	const FULL_TABLE_DUMP = "SELECT id, name, file_maker_layout, active FROM bs_tool_types WHERE active = 1 ";

	/**
	 * Extra query parameter used with $FULL_TABLE_DUMP
	 */
	const ADDITIONAL_CLAUSES = " GROUP BY id ";

	/**
	* Unique tool type id
	* DB column: bs_tool_types.id.
	* 
	* @var int $id
	*/
	private $id;

	/**
	* Name of the tool type
	* DB column: bs_tool_types.name.
	*
	* @var string $name
	*/
	private $name;

	/**
	* Layout specific to file maker software
	* DB column: bs_tool_types.file_maker_layout.
	*
	* @var string $fileMakerLayout
	*/
	private $fileMakerLayout;

	/**
	* Where or not the tool type is active or not
	* DB column: bs_tool_types.active.
	*
	* @var bool $active
	*/
	private $active;

	/**
	 * Construct will handle setting/calling
	 * the setters methods
	 * 
	 * @param int $id
	 * @throws trigger
	 */
	public function __construct($id = NULL) {
		
		 // Set the ID.
        $this->setId($id);


		if( !is_null($this->getId()) ) {

			// Get cache dir
			CacheableEntity::__construct(get_class($this), $this->getId());

			// Attempt to get data from cache
			$data = $this->getCache();

			if( empty($data) ) {

				$query = Connection::getHandle()->prepare(self::FULL_TABLE_DUMP . " AND id = :id ");

				$query->bindParam(':id', $this->getId(), PDO::PARAM_INT);

				if( $query->execute() ) {

					$data = $query->fetch(PDO::FETCH_ASSOC);
					$this->storeCache($data);
				}
			}

			//Setters
			$this->setName($data['name'])
				 ->setActive($data['active'])
				 ->setFileMakerLayout($data['file_maker_layout']);

		} else {

			// Trigger a notice if an invalid ID was supplied.
			trigger_error('Cannot load ToolType properties: \'' . $id . '\' is not a valid ID number.');

		}
	}

	/*************************************************
	* Start Setters 
	**************************************************/
	/**
	 * Set privately the $id and return $this
	 *
	 * @param  int $id
	 * @return ToolType()
	 */
	private function setId($id = NULL) {

		$this->id = isset($id) && is_numeric($id) && $id > 0 ? (int) $id : NULL;

		return $this;
	}

	/**
	* Set privately the $name and return $this
	*
	* @param string $name
	* @return ToolType()
	*/	
	public function setName($name = '') {

		$this->name = !empty($name) ? trim($name) : NULL;
		
		return $this;
	}

	/**
	* Set privately the $fileMakerLayout and return $this
	* @param string $fileMakerLayout
	* @return ToolType()
	*/	
	public function setFileMakerLayout($fileMakerLayout = '') {

		$this->fileMakerLayout = !empty($fileMakerLayout) ? trim(self::FILEMAKER_LAYOUT_PATH.$fileMakerLayout) : NULL;

		return $this;
	}

	/**
	 * Set the $active and return $this
	 *
	 * @param bool|int $active
	 * @return ToolType()
	 */
	public function setActive($active = FALSE) {
		$this->active = (bool) $active;
		return $this;
	}

	/*************************************************
	* Start Getters 
	**************************************************/
	//Get the Id
	public function getId() { return $this->id; }
	
	//Get the Name
	public function getName() { return $this->name; }
	
	//Get the Activation 
	public function isActive() { return $this->active; } 
	
	//Get the File  Maker Layout
	public function getFileMakerLayout() { return $this->fileMakerLayout; }

    public function getIdByName($name = NULL ){

        $sql_name = "SELECT id FROM bs_tool_types WHERE name = :name AND active = TRUE";

        $sql_row = Connection::getHandle()->prepare($sql_name);

        $sql_row->bindParam(':name', $name, PDO::PARAM_INT);

        if( $sql_row->execute() ) {

            $data = $sql_row->fetch(PDO::FETCH_ASSOC);

        }

        return $data['id'];

    }
	
	//Create an instance of $this.
	public static function create($id = NULL) { return new self($id); }
}
