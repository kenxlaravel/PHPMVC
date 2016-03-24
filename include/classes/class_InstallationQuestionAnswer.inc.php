<?php

/**
 * Class InstallationQuestionAnswer
 */
class InstallationQuestionAnswer extends CacheableEntity {

    /**
     * Constant used for two purposes
     *
     *  - Getting the record from the database
     *  - FULL_TABLE_DUMP is a keyword used for our cache system so it can know what query to run
     *
     */
	const FULL_TABLE_DUMP = "SELECT id, question_id, answer, followup_question_id,
			 	                installation_method_id, position, active
				             FROM bs_installation_question_answers WHERE active = 1 ";

    /**
     * Extra query parameter used with $FULL_TABLE_DUMP
     */
	const ADDITIONAL_CLAUSES = "GROUP BY id";

	/**
	* Unique question answer identifier
	* DB column: bs_installation_question_answers.id
	* 
	* @var int $id
	*/
	private $id;

    /**
     * @var int $InstallationMethodId;
     */
    private $InstallationMethodId;

	/**
	* Question id of the actual question
	* DB column: bs_installation_question_answers.question_id
	* 
	* @var int $question_id
	*/
	private $questionId;

	/**
	* Actual answer string
	* DB column: bs_installation_question_answers.answer
	* 
	* @var string $answer
	*/
	private $answer;

	/**
	* Question Object
	* DB table: bs_installation_questions
	* 
	* @var object $FollowupQuestion
	*/
	private $FollowUpQuestion;

	/**
	* InstallationMethod Object
	* DB table: bs_installation_methods
	* 
	* @var InstallationMethod $InstallationMethod
	*/
	private $InstallationMethod;

	/**
	 * Array of Installation Methods
	 *
	 * @var array $installationMethodId
	 */
	private $installationMethodId;

	/**
	* Position of answer
	* DB column: bs_installation_question_answers.position
	* 
	* @var int $position
	*/
	private $position;

	/**
	* Whether or not the answer is active
	* DB column: bs_installation_question_answers.active
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

			CacheableEntity::__construct(get_class($this), $this->getId());

            $data = $this->getCache();

			if( empty($data) ) {

				$query = Connection::getHandle()->prepare(self::FULL_TABLE_DUMP." AND question_id = :id ");
				$query->bindParam(':id', $this->getId(), PDO::PARAM_INT);

				if( $query->execute() ) {

					while ($data = $query->fetch(PDO::FETCH_ASSOC)) {

						$this->setQuestionId($data['question_id'])
							 ->setAnswer($data['answer'])
							 ->setFollowUpQuestion($data['followup_question_id'])
							 ->setPosition($data['position'])
							 ->setActive($data['active'])
							 ->setInstallationMethod($data['installation_method_id']);
					}
				}
			}
		} else {
			// Trigger a notice if an invalid ID was supplied.
			trigger_error('Cannot load properties: \'' . $id . '\' is not a valid ID number.');
		}
	}

	/*************************************************
	* Start Setters 
	**************************************************/
	/**
	* Set privately the $id and return $this
	* 
	* @param int $id
	* @return InstallationQuestionAnswer() Return current class object
	*/
	private function setId($id) {

		$this->id = isset($id) && is_numeric($id) && $id > 0 ? (int) $id : NULL;

		return $this;
	}

	/**
	* Set the $questionId and return InstallationQuestionAnswer()
	* 
	* @param int $questionId
	* @return InstallationQuestionAnswer Return current class object
	*/
	public function setQuestionId($questionId) {

		$this->questionId = isset($questionId) && is_numeric($questionId) && 
							$questionId > 0 ? (int) $questionId : NULL;
		return $this;
	}

	/**
	* Set the $answer and return InstallationQuestionAnswer()
	* 
	* @param string $answer
	* @return InstallationQuestionAnswer Return current class object
	*/
	public function setAnswer($answer) {

		$this->answer[] = !empty($answer) ? trim($answer) : NULL;
		
		return $this;
	}

	/**
	* Set the $FollowUpQuestionId and return class 
	* object of Question()
	* 
	* @param array $FollowUpQuestionId
	* @return InstallationQuestionAnswer Return current class object
	*/
	public function setFollowUpQuestion($FollowUpQuestionId) {

		$this->FollowUpQuestion[] = isset($FollowUpQuestionId) && is_numeric($FollowUpQuestionId) && 
										  $FollowUpQuestionId > 0 ? $FollowUpQuestionId : NULL;
		return $this;
	}

	/**
	* Set the $InstallationMethodId and return class 
	* object of InstallationMethod()
	* 
	* @param array $InstallationMethodId
	* @return InstallationQuestionAnswer Return current class object
	*/
	public function setInstallationMethod($InstallationMethodId) {
		
		$this->InstallationMethod[] = $InstallationMethodId;

		return $this;
	}

	/**
	* Set the $position and return InstallationQuestionAnswer()
	* 
	* @param int $position
	* @return InstallationQuestionAnswer Return current class object
	*/
	public function setPosition($position) {

		$this->position = (int) $position;
		return $this;
	}

	/**
	* Set the $active and return InstallationQuestionAnswer()
	* 
	* @param bool $active
	* @return InstallationQuestionAnswer Return current class object
	*/
	public function setActive($active) {
		$this->active = (bool) $active;
		return $this;
	}


	/*************************************************
	* Start Getters 
	**************************************************/
    /**
     * @return int
     */
	public function getId() { return $this->id; }

    /**
     * @return int
     */
    public function getQuestionId() { return $this->questionId; }

    /**
     * @return string
     */
    public function getAnswer() { return $this->answer; }

    /**
     * @return object
     */
    public function getFollowUpQuestion() {
	
		foreach( $this->FollowUpQuestion as $FollowUpQuestionId ) {

			$this->FollowUpQuestion[$FollowUpQuestionId][] = InstallationQuestion::create($FollowUpQuestionId);
			$this->FollowUpQuestion[$FollowUpQuestionId][] = $this->getAnswer();
		}

		return $this->FollowUpQuestion; 
	}

    /**
     * @return InstallationMethod
     */
    public function getInstallationMethod() {
		
		if( !empty($this->installationMethodId) ) {

			$this->InstallationMethod = InstallationMethod::create($this->InstallationMethodId);
		}

		return $this->InstallationMethod; 
	}

    /**
     * @return int
     */
    public function getPosition() { return $this->position; }

    /**
     * @return bool
     */
    public function isActive() { return $this->active; }

    /**
     * @param $id
     * @return InstallationQuestionAnswer
     */
    public static function create($id) { return new self($id); }
}