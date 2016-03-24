<?php

/**
 * Class InstallationQuestion
 */
class InstallationQuestion extends CacheableEntity {

    /**
     * Constant used for two purposes
     *
     * - Getting the record from the database
     * - FULL_TABLE_DUMP is a keyword used for our cache system so it can know what query to run
     */
	const FULL_TABLE_DUMP = "SELECT id, question, active FROM bs_installation_questions WHERE active = 1 ";

    /**
     * Extra query parameter used with $FULL_TABLE_DUMP
     */
	const ADDITIONAL_CLAUSES = "GROUP BY id";

	/**
	* Unique question identifier
	* DB column: bs_installation_questions.id
	* 
	* @var int $id
	*/
	private $id;

	/**
	* The actual question string
	* DB column: bs_installation_questions.question
	* 
	* @var string $question
	*/
	private $question;

	/**
	* An array of QuestionAnswer objects, indexed by bs_installation_question_answer.id
	* DB table: bs_installation_question_answer
	* 
	* @var array $questionAnswers
	*/
	private $questionAnswers;

	/**
	 * An array of Question ids
	 * DB column: bs_installation_questions.id;
	 *
	 * @var array $questionId;
	 */
	private $questionId;

	/**
	* Whether or not the question is active
	* DB column: bs_installation_questions.active
	* 
	* @var bool $active
	*/
	private $active;

	/**
	 * Construct will handle setting calling
	 * the setters methods
	 * 
	 * @param int $id Id used to query records from bs_installation_question_answer
	 */
	public function __construct($id) {

		$this->setId($id);

		if ( !is_null($this->getId()) ) {

			if( empty($data) ) {

				$query = Connection::getHandle()->prepare(self::FULL_TABLE_DUMP." AND id = :id ");

				$query->bindParam(':id', $this->getId(), PDO::PARAM_INT);

				if( $query->execute() ) {

					while ($data = $query->fetch(PDO::FETCH_ASSOC)) {

						$this->setQuestionAnswers($data['id'])
							 ->setQuestion($data['question'])
							 ->setActive($data['active']);
					}
				}
			}
	   } else {

			//Trigger a notice if an invalid ID was supplied.
			trigger_error('Cannot load properties: \'' . $id . '\' is not a valid ID number.');
		}
	}

	/*************************************************
	* Start Setters 
	**************************************************/
	/**
	 * Set privately the $id and return $this
	 * 
	 * @param  int 	  $id
	 * @return object Return current object pointer
	 */	
	private function setId($id) {

		$this->id = isset($id) && is_numeric($id) && $id > 0 ? (int) $id : NULL;

		return $this;
	}

	/**
	 * Set $question of for the installationMethod table
	 * Trim() any spaces at the start/end of the string
	 * 
	 * @param string $question bs_installation_question_questions.question
	 * @return object Return current object pointer
	 */
	public function setQuestion($question) {

		$this->question[] = !empty($question) ? trim($question) : NULL;

		return $this;
	}

	/**
	 * Create $questionAnswerId object
	 * Hold instance of InstallationQuestionAnswer() 
	 * 
	 * @see class_InstallationQuestionAnswer.inc.php
	 * @param int $questionAnswerId Id's returned from Instance IQA ()
	 * @return object Return current object pointer
	 */
	public function setQuestionAnswers($questionAnswerId) {

		$this->questionId[] = isset($questionAnswerId) && is_numeric($questionAnswerId) && 
								  $questionAnswerId > 0 ? (int) $questionAnswerId : NULL;
		return $this;
	}

	/**
	 * Set $active value
	 * Typecast (bool) conversion to true or false 
	 * 
	 * @param bool $active bs_installation_methods.active
	 * @return object Return current object pointer
	 */
	public function setActive($active = FALSE) {

		$this->active = (bool) $active;
		return $this;
	}

   /**************************************************
	* Start Getters  								 *
	**************************************************/
    /**
     * @return int
     */
	public function getId() { return $this->id; }

    /**
     * @return string
     */
    public function getQuestion() { return $this->question; }

    /**
     * @return array
     */
    public function getQuestionAnswers() {

		if( isset($this->questionId) ) {

			foreach( $this->questionId as $id ) {

				$this->questionAnswers = InstallationQuestionAnswer::create($id);
			}
		}

		return $this->questionAnswers; 
	}

    /**
     * @return int|bool
     */
    public function isActive() { return $this->active; }

    /**
     * @param $answerId
     * @return mixed
     */
    public function getInstallationMethod($answerId) {

		if( !empty($this->questionAnswers[$answerId]) ) {

			return $this->questionAnswers[$answerId]->getInstallationMethod();
		}
	}

    /**
     * @param null $id
     * @return InstallationQuestion
     */
    public static function create($id = NULL) { return new self($id); }

}