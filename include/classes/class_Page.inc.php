<?php

class Page extends CacheableEntity {

    /**
     * @var null|string $type
     */
	private $type = null;

    /**
     * @var int $id
     */
    private $id = 0;

    /**
     * @var null|string
     */
    private $nickname = null;

    /**
     * @var int|bool $validity
     */
    private $validity = false;

    /**
     * @var null|string $name
     */
    private $name = null;

    /**
     * @var null|string $shortUrl
     */
    private $shortUrl = null;

    /**
     * @var int|bool $secure
     */
    private $secure = false;

    /**
     * @var null|string $slug
     */
    private $slug = null;

    /**
     * @var null|string $filename
     */
    private $filename = null;

    /**
     * @var int $canonical
     */
    private $canonical = 0;

    /**
     * @var null|string $title
     */
    private $title = null;

    /**
     * @var null|string $metaDescription
     */
    private $metaDescription  = null;

	/**
	 * @var null Image of Category, Subcategory, Grouping, or Landing
	 */
	private $image = null;

    /**
     * @var null|string $metaKeywords
     */
    private $metaKeywords = null;

    /**
     * @var null|string $heading
     */
    private $heading = null;

    /**
     * @var int|bool $allowTarget
     */
    private $allowTarget = false;

    /**
     * @var int|bool $requiresLogin
     */
    private $requiresLogin = false;

    /**
     * @var int|bool $disallowGuest
     */
    private $disallowGuests = false;

    /**
     * @var int|bool $visibility
     */
    private $visibility = false;

    /**
     * @var int $priority
     */
    private $priority = 0;

    /**
     * @var null|int $changeFrequency
     */
    private $changeFrequency = null;

    /**
     * @var null|string $url
     */
    private $url = null;

    /**
     * @var null|string $requestUrl
     */
    private $requestUrl = null;

    /**
     * @var null|string $requestPath
     */
    private $requestPath = null;

    /**
     * @var null|int $parentId
     */
    private $parentId = NULL;

    /**
     * @var null|string $ParentPage
     */
    private $ParentPage = NULL;

    /**
     * @var null|string $parentPageType
     */
    private $parentPageType = NULL;

    /**
     * @var array $breadCrumbs
     */

    private $tracking = array();

    /**
     * Construct will handle setting calling
     * the setters methods
     *
     * @param int $id Id used to query records from bs_material
     * @param string $type
     * @param string $data
     */
	public function __construct($type, $id = null, $data = null) {

		// If a nickname was provided instead of a type and ID, get the type and ID from the database.
		if ( isset($type) && !isset($id) ) {

			list($type, $id) = $this->getArgsFromNickname($type);
		}

		// If a type and ID were either provided or found in the database via the nickname, set the class properties.
		if (isset($type, $id)) {

			//If the page data was already passed in, set it as class properties
			if ( !empty($data) ) {

			 	$this->setProperties($type, $data);

			} else {

				$this->getProperties($type, $id);
			}
		}

		// If we are calling this from CLI (e.g. as a cron job), we cannot get the requested URL.
		// Instead, assume the cron job always calls proper URLs (will still 404 if the page does not
		// exist, but will never 301 if URL is malformed.)
		if ( isset($_SERVER['REQUEST_URI']) ) {

			$this->requestUrl   = $_SERVER['REQUEST_URI'];
			$request_parts      = parse_url($this->requestUrl);
			$this->requestPath  = $request_parts["path"];
		}
	}

	// instantiate correct child class and return object
    /**
     * @param $type
     * @param $id
     * @return GeotargetPage|GroupingPage|LandingPage|null|Page|ProductPage|SubcategoryPage
     */
    public static function getPageByTypeAndId($type, $id) {

		switch ($type) {

			case 'geotarget':
				$Page = new GeotargetPage($id);
				break;

			case 'product':
				$Page = new ProductPage($id);
				break;

			case 'subcategory':
				$Page = SubcategoryPage::create($id);
				break;

			case 'grouping':

				$Page = isset($id) ? GroupingPage::create($id) : NULL;
				break;

			case 'category':
				$Page = CategoryPage::create($id);
                break;

			case 'landing':
				$Page = LandingPage::create($id);
                break;

			default:
				$Page = Page::create($type, $id);
				break;
		}

		return is_object($Page) ? $Page : NULL;
	}

	/**
	* Gets ParentPage.
	*
	* @return Page() object
	*  TODO: UNCOMMENT THIS.
	*/
	public function getParentPage() {

		if ( defined(get_class($this)."::PARENT_TYPE") ) {

			$this->parentPageType = constant(get_class($this) . "::PARENT_TYPE");

			if ( !is_null($this->parentPageType) ) {

				$this->ParentPage = self::getPageByTypeAndId($this->parentPageType, $this->parentId);

			}
		}

		return $this->ParentPage;
	}

	/*
	* Retrieves the subsequent pages that lead to the current page
	*
	* @return [$breadCrumbs]   [array]   [An array of pages and their ancestry]
	*/
    /**
     * @return array
	 * TODO: UNCOMMENT THIS
     */
    public function getBreadcrumbs() {

        $breadcrumbs = array ();
        //The home crumb
        $breadcrumbs[1]['text'] = 'SafetySign.com';
        $breadcrumbs[1]['link'] = URL_PREFIX_HTTP;

        $stateParameters = array();

        if ( isset($_GET['s']) ) {

            $stateParameters = ProductStateParameter::decode($_GET['s']);

            $sourceSubcategoryProduct = $stateParameters['sourceSubcategoryProduct'];
            $sourceLandingProduct = $stateParameters['sourceLandingProduct'];
            $breadcrumbSubcategory = $stateParameters['breadcrumbSubcategory'];
            $breadcrumbLanding = $stateParameters['breadcrumbLanding'];

        }

        switch ($this->type) {
            case 'category':
                break;
            case 'landing':
                $breadcrumbs[2]['text'] = $this->getName();
                $breadcrumbs[2]['link'] = $this->url;
                $breadcrumbs[2]['type'] = $this->type;
                $breadcrumbs[2]['id'] = $this->id;
                break;
            case 'page':
                $breadcrumbs[2]['text'] = $this->getName();
                $breadcrumbs[2]['link'] = $this->url;
                $breadcrumbs[2]['type'] = $this->type;
                $breadcrumbs[2]['id'] = $this->id;
                break;
            case 'grouping':
                $sql = Connection::getHandle()->prepare(
                    "SELECT category_id AS category_id
								    	    FROM bs_groupings
								    	    WHERE id=? LIMIT 1"
                );
                $sql->execute(array ($this->id));
                $row = $sql->fetch(PDO::FETCH_ASSOC);
                $link2 = new Page('category', $row['category_id']);
                $breadcrumbs[2]['text'] = $link2->getName();
                $breadcrumbs[2]['link'] = $link2->getUrl();
                $breadcrumbs[2]['type'] = $link2->getType();
                $breadcrumbs[2]['id'] = $link2->getId();
                $breadcrumbs[3]['text'] = $this->getName();
                $breadcrumbs[3]['link'] = $this->url;
                $breadcrumbs[3]['type'] = $this->type;
                $breadcrumbs[3]['id'] = $this->id;
                break;
            case 'subcategory':
                $sql = Connection::getHandle()->prepare(
                    "SELECT g.category_id AS category_id, s.grouping_id AS grouping_id
								    	    FROM bs_subcategories s
								    	    LEFT JOIN bs_groupings g ON (s.grouping_id = g.id)
								    	    WHERE s.id=? LIMIT 1"
                );
                $sql->execute(array ($this->id));
                $row = $sql->fetch(PDO::FETCH_ASSOC);
                $link2 = new Page('category', $row['category_id']);
                $breadcrumbs[2]['text'] = $link2->getName();
                $breadcrumbs[2]['link'] = $link2->getUrl();
                $breadcrumbs[2]['type'] = $link2->getType();
                $breadcrumbs[2]['id'] = $link2->getId();
                $link3 = new Page('grouping', $row['grouping_id']);
                $breadcrumbs[3]['text'] = $link3->getName();
                $breadcrumbs[3]['link'] = $link3->getUrl();
                $breadcrumbs[3]['type'] = $link3->getType();
                $breadcrumbs[3]['id'] = $link3->getId();
                $breadcrumbs[4]['text'] = $this->getName();
                $breadcrumbs[4]['link'] = $this->url;
                $breadcrumbs[4]['type'] = $this->type;
                $breadcrumbs[4]['id'] = $this->id;
                break;
            case 'geotarget':
                $sql = Connection::getHandle()->prepare(
                    "SELECT g.category_id AS category_id, s.grouping_id AS grouping_id, t.subcategory_id AS subcategory_id
								    	    FROM bs_subcategories s
								    	    LEFT JOIN bs_groupings g ON (s.grouping_id = g.id)
								    	    LEFT JOIN bs_subcategories_geotargeted t ON (t.subcategory_id = s.id)
								    	    WHERE t.id=? LIMIT 1"
                );
                $sql->execute(array ($this->id));
                $row = $sql->fetch(PDO::FETCH_ASSOC);
                $link2 = new Page('category', $row['category_id']);
                $breadcrumbs[2]['text'] = $link2->getName();
                $breadcrumbs[2]['link'] = $link2->getUrl();
                $breadcrumbs[2]['type'] = $link2->getType();
                $breadcrumbs[2]['id'] = $link2->getId();
                $link3 = new Page('grouping', $row['grouping_id']);
                $breadcrumbs[3]['text'] = $link3->getName();
                $breadcrumbs[3]['link'] = $link3->getUrl();
                $breadcrumbs[3]['type'] = $link3->getType();
                $breadcrumbs[3]['id'] = $link3->getId();
                $link4 = new Page('subcategory', $row['subcategory_id']);
                $breadcrumbs[4]['text'] = $link4->getName();
                $breadcrumbs[4]['link'] = $link4->getUrl();
                $breadcrumbs[4]['type'] = $link4->getType();
                $breadcrumbs[4]['id'] = $link4->getId();
                $breadcrumbs[5]['text'] = $this->getName();
                $breadcrumbs[5]['link'] = $this->url;
                $breadcrumbs[5]['type'] = $this->type;
                $breadcrumbs[5]['id'] = $this->id;
                break;
            case 'product':
                if ( !isset($breadcrumbLanding) && isset($breadcrumbSubcategory) ) {

                    //Base/Default query
                    $query = 'SELECT g.category_id AS category_id, s.grouping_id AS grouping_id, sp.subcategory_id AS subcategory_id

                            FROM bs_products p

                            INNER JOIN bs_subcategory_products sp ON(sp.product_id = p.id)
                            INNER JOIN bs_subcategories s ON(s.id = sp.subcategory_id)
                            INNER JOIN bs_groupings g ON(g.id = s.grouping_id)

                            WHERE sp.id = ? AND s.id = ? ';

                    $params = array ($sourceSubcategoryProduct, $breadcrumbSubcategory);

                    $sql = Connection::getHandle()->prepare($query);

                    $sql->execute($params);

                    $row = $sql->fetch(PDO::FETCH_ASSOC);
                    $link2 = Page::create('category', $row['category_id']);
                    $breadcrumbs[2]['text'] = $link2->getName();
                    $breadcrumbs[2]['link'] = $link2->getUrl();
                    $breadcrumbs[2]['type'] = $link2->getType();
                    $breadcrumbs[2]['id'] = $link2->getId();
                    $link3 = new Page('grouping', $row['grouping_id']);
                    $breadcrumbs[3]['text'] = $link3->getName();
                    $breadcrumbs[3]['link'] = $link3->getUrl();
                    $breadcrumbs[3]['type'] = $link3->getType();
                    $breadcrumbs[3]['id'] = $link3->getId();
                    $link4 = new Page('subcategory', $row['subcategory_id']);
                    $breadcrumbs[4]['text'] = $link4->getName();
                    $breadcrumbs[4]['link'] = $link4->getUrl();
                    $breadcrumbs[4]['type'] = $link4->getType();
                    $breadcrumbs[4]['id'] = $link4->getId();
                    $breadcrumbs[5]['text'] = $this->getName();
                    $breadcrumbs[5]['link'] = $this->url;
                    $breadcrumbs[5]['type'] = $this->type;
                    $breadcrumbs[5]['id'] = $this->id;

                } else if ( isset($breadcrumbLanding) ) {

                    $sql = Connection::getHandle()->prepare(
                        "SELECT lp.landing_id AS landing_id

                            FROM bs_products p

                            INNER JOIN bs_landing_products lp ON(lp.product_id = p.id)
                            INNER JOIN bs_landings bl ON(bl.id = lp.landing_id)

                            WHERE lp.id = ? AND bl.id = ?"
                    );

                    $sql->execute(array ($sourceLandingProduct, $breadcrumbLanding));

                    $row = $sql->fetch(PDO::FETCH_ASSOC);
                    $link2 = Page::create('landing', $row['landing_id']);
                    $breadcrumbs[2]['text'] = $link2->getName();
                    $breadcrumbs[2]['link'] = $link2->getUrl();
                    $breadcrumbs[2]['type'] = $link2->getType();
                    $breadcrumbs[2]['id'] = $link2->getId();
                    $breadcrumbs[5]['text'] = $this->getName();
                    $breadcrumbs[5]['link'] = $this->url;
                    $breadcrumbs[5]['type'] = $this->type;
                    $breadcrumbs[5]['id'] = $this->id;

                } else {

                    $sql = Connection::getHandle()->prepare(
                        "SELECT
                            g.category_id AS category_id,
                            s.grouping_id AS grouping_id,
                            p.default_subcategory_id AS subcategory_id
                        FROM
                            bs_products p
                        INNER JOIN bs_subcategories s ON (s.id = p.default_subcategory_id)
                        INNER JOIN bs_groupings g ON (g.id = s.grouping_id)
                        WHERE
                            p.id = ?"
                    );

                    $sql->execute(array($this->id));

                    $row = $sql->fetch(PDO::FETCH_ASSOC);

                    $link2 = Page::create('category', $row['category_id']);
                    $breadcrumbs[2]['text'] = $link2->getName();
                    $breadcrumbs[2]['link'] = $link2->getUrl();
                    $breadcrumbs[2]['type'] = $link2->getType();
                    $breadcrumbs[2]['id'] = $link2->getId();
                    $link3 = new Page('grouping', $row['grouping_id']);
                    $breadcrumbs[3]['text'] = $link3->getName();
                    $breadcrumbs[3]['link'] = $link3->getUrl();
                    $breadcrumbs[3]['type'] = $link3->getType();
                    $breadcrumbs[3]['id'] = $link3->getId();
                    $link4 = new Page('subcategory', $row['subcategory_id']);
                    $breadcrumbs[4]['text'] = $link4->getName();
                    $breadcrumbs[4]['link'] = $link4->getUrl();
                    $breadcrumbs[4]['type'] = $link4->getType();
                    $breadcrumbs[4]['id'] = $link4->getId();
                    $breadcrumbs[5]['text'] = $this->getName();
                    $breadcrumbs[5]['link'] = $this->url;
                    $breadcrumbs[5]['type'] = $this->type;
                    $breadcrumbs[5]['id'] = $this->id;

                    if ( empty($row) ) {

                        $sql = Connection::getHandle()->prepare(
                            "SELECT p.default_landing_id AS landing_id

                        FROM bs_products p

                        WHERE p.id = ?"
                        );

                        $sql->execute(array($this->id));

                        $row = $sql->fetch(PDO::FETCH_ASSOC);

                        $link2 = Page::create('landing', $row['landing_id']);
                        $breadcrumbs[2]['text'] = $link2->getName();
                        $breadcrumbs[2]['link'] = $link2->getUrl();
                        $breadcrumbs[2]['type'] = $link2->getType();
                        $breadcrumbs[2]['id'] = $link2->getId();
                        $breadcrumbs[5]['text'] = $this->getName();
                        $breadcrumbs[5]['link'] = $this->url;
                        $breadcrumbs[5]['type'] = $this->type;
                        $breadcrumbs[5]['id'] = $this->id;

                    }




                }

                break;

            case 'help':
                $sql = Connection::getHandle()->prepare(
                    "SELECT s.name AS section, s.id AS section_id, h.name AS help
								    	    FROM bs_help h
								    	    LEFT JOIN bs_help_sections s ON (h.help_section_id = s.id)
								    	    WHERE h.id=? LIMIT 1"
                );
                $sql->execute(array ($this->id));
                $row = $sql->fetch(PDO::FETCH_ASSOC);
                $link = new Page('help');
                $breadcrumbs[2]['text'] = $link->getName();
                $breadcrumbs[2]['link'] = $link->getUrl();
                if( $row['help'] != 'Help' ) {
                    $breadcrumbs[3]['text'] = $row['section'];
                    $breadcrumbs[3]['id'] = $row['section_id'];
                    $breadcrumbs[4]['text'] = $row['help'];
                    $breadcrumbs[4]['id'] = $this->id;
                    $breadcrumbs[4]['link'] = $this->url;
                }
                break;
        }

        foreach ($breadcrumbs AS $types) {

            if( isset($types['type']) ) {
                $this->tracking[] = array ("id" => $types['id'], "type" => $types['type']);
            }
        }

        return $breadcrumbs;
    }

    public function getBreadCrumbData() {

        return $this->breadCrumbs;
    }

	// Getters
    /**
     * @return int|null
     */
    public function getChangeFrequency() { return $this->changeFrequency; }

    /**
     * @return null|string
     */
    public function getFilename() { return $this->filename; }

    /**
     * @return null|string
     */
    public function getHeading() { return $this->heading; }

    /**
     * @return int
     */
    public function getId() { return $this->id; }

    /**
     * @return null|string
     */
    public function getMetaDescription() { return $this->metaDescription; }



	/**
	 * @return string Image path
	 */
	public function getImage() { return $this->image; }

    /**
     * @return null|string
     */
    public function getMetaKeywords() { return $this->metaKeywords; }

    /**
     * @return null|string
     */
    public function getName() { return $this->name; }

    /**
     * @return null|string
     */
    public function getNickname() { return $this->nickname; }

    /**
     * @return int
     */
    public function getPriority() { return $this->priority; }

    /**
     * @return bool|int
     */
    public function getSecure() { return $this->secure; }

    /**
     * @return null|string
     */
    public function getShortUrl() { return $this->shortUrl; }

    /**
     * @return null|string
     */
    public function getSlug() { return $this->slug; }

    /**
     * @return null|string
     */
    public function getTitle() { return $this->title; }

    /**
     * @return null|string
     */
    public function getType() { return $this->type; }

    /**
     * @return null|string
     */
    public function getUrl() { return $this->url; }

    /**
     * @return bool|int
     */
    public function getAllowTarget() { return $this->allowTarget; }

    /**
     * @return bool|int
     */
    public function getRequiresLogin() { return $this->requiresLogin; }

    /**
     * @return bool|int
     */
    public function getDisallowGuests() { return $this->disallowGuests; }

    /**
     * @return bool|int
     */
    public function getValidity() { return $this->validity; }

    /**
     * @return bool|int
     */
    public function getVisibility() { return $this->visibility; }

    /**
     * @return int|null
     */
    public function getCanonicalId() { return ($this->nickname == '404' ? NULL : $this->canonical); }

    /**
     * @return null|string
     */
    public function getCanonicalUrl() {

		if ($this->nickname == '404') {

			return NULL;

		} else if ($this->canonical != $this->id) {

			$link = new Page($this->type, $this->canonical);

			return $link->getUrl();

		} else {

			return $this->url;
		}
	}

    /**
     * @return bool
     */
    public function isCanonical() {

		return (($this->canonical == $this->id) || ($this->nickname == '404') ? TRUE : FALSE);
	}

	// Setters
    /**
     * @param $canonical
     * @return $this
     */
    public function setCanonical($canonical) {

		if ( isset($canonical) ) {

			$this->canonical = (int) $canonical;
		}

		return $this;
	}

    /**
     * @param $change_frequency
     * @return $this
     */
    private function setChangeFrequency($change_frequency) {

		if ( $this->validateChangeFrequency($change_frequency) ) {

			$this->changeFrequency = trim(mb_strtolower($change_frequency));
		}

		return $this;
	}

    /**
     * @param $filename
     * @return $this
     */
    private function setFilename($filename) {

		if ( isset($filename) ) {

			$this->filename = APP_ROOT . '/' . $filename;
		}

		return $this;
	}

    /**
     * @param $heading
     * @return $this
     */
    private function setHeading($heading) {

        $this->heading = !empty($heading) ? trim($heading) : NULL;

		return $this;
	}

    /**
     * @param $id
     * @return $this
     */
    private function setId($id) {

		if ( $this->validateId($id) ) $this->id = (int) $id;

		return $this;
	}

    /**
     * @param $meta_description
     * @return $this
     */
    private function setMetaDescription($meta_description) {

		$this->metaDescription = !empty($meta_description) ? trim($meta_description) : NULL;

		return $this;
	}

	private function setImage($image = NULL) {

		$this->image = $image;
		return $this;

	}

    /**
     * @param $meta_keywords
     * @return $this
     */
    private function setMetaKeywords($meta_keywords) {

		$this->metaKeywords = !empty($meta_keywords) ? trim($meta_keywords) : NULL;

		return $this;
	}

    /**
     * @param $name
     * @return $this
     */
    private function setName($name) {

		$this->name = !empty($name) ? trim($name) : NULL;

		return $this;
	}

    /**
     * @param [string] $nickname
     * @return Page() object
     */
	private function setNickname($nickname) {

		$this->nickname = !empty($nickname) ? trim($nickname) : NULL;

		return $this;
	}

    /**
     * @param $priority
     * @return Page() object
     */
	private function setPriority($priority) {

		if ( isset($priority) ) {

			$this->priority = (float) round($priority, 1);
		}

		return $this;
	}

    /**
     * @param bool $secure
     * @return Page()
     */
	private function setSecure($secure = FALSE) {

		if ( $secure ) {

			$this->secure = (bool) $secure;
		}

		return $this;
	}

    /**
     * @param $short_url
     * @return $this
     */
    private function setShortUrl($short_url) {

		$this->shortUrl = !empty($short_url) ? trim($short_url) : NULL;

		return $this;
	}

    /**
     * @param $slug
     * @return $this
     */
    private function setSlug($slug) {

		$this->slug = !empty($slug) ? trim($slug) : NULL;

		return $this;
	}

    /**
     * @param $title
     * @return $this
     */
    private function setTitle($title) {

		$this->title = !empty($title) ? trim($title) : NULL;

		return $this;
	}

    /**
     * @param $type
     * @return $this
     */
    private function setType($type) {

		if ( $this->validateType($type) ) {

			$this->type = trim(mb_strtolower($type));
		}

		return $this;
	}

    /**
     * @param $url
     * @return $this
     */
    private function setUrl($url) {

		$this->url = !empty($url) ? trim($url) : NULL;

		return $this;
	}

    /**
     * @param $allow_target
     * @return $this
     */
    private function setAllowTarget($allow_target) {

		if ( isset($allow_target) ) {

			$this->allowTarget = (bool) $allow_target;
		}

		return $this;
	}

    /**
     * @param $requires_login
     * @return $this
     */
    private function setRequiresLogin($requires_login) {

    	if ( isset($requires_login) ) {

    		$this->requiresLogin = (bool) $requires_login;
    	}

    	return $this;
    }

    /**
     * @param $disallow_guests
     * @return $this
     */
    private function setDisallowGuests($disallow_guests) {

    	if ( isset($disallow_guests) ) {

    		$this->disallowGuests = (bool) $disallow_guests;
    	}

    	return $this;
    }

    /**
     * Set the validity for the current page
     *
     * @param $validity
     * @return $this
     */
    private function setValidity($validity) {

		if ( isset($validity) ) {

			$this->validity = (bool) $validity;
		}

		return $this;
	}

    /**
     * Set the visibility for the current page
     *
     * @param $visibility
     * @return $this
     */
    private function setVisibility($visibility) {

		if ( isset($visibility) ) {

			$this->visibility = (bool) $visibility;
		}

		return $this;
	}

	// Validators
    /**
     * Check/change the frequency and return true or false
     *
     * @param $change_frequency
     * @return bool
     */
    private function validateChangeFrequency ($change_frequency) {

        return (isset($change_frequency) && in_array(trim(mb_strtolower($change_frequency)),
                array("always","hourly","daily","weekly","monthly","yearly","never"))) ? true : false;
    }

    /**
     * Set and validate the id
     *
     * @param $id
     * @return bool
     */
    private function validateId($id) { return (isset($id) && (int) $id > 0) ? true : false; }

    /**
     * @param $type
     * @return bool
     */
    private function validateType($type) {

        return (isset($type) && in_array(trim(mb_strtolower($type)),
                array("page","landing","category","grouping","subcategory","geotarget","product","help"))) ? true : false;
    }

	/**
	 * Returns a page type and ID from a page nickname
	 *
	 * @param  [string] $nickname A page nickname
	 * @return array  $args  An array containing page type and ID
	 */
	private function getArgsFromNickname($nickname) {

		$args = array();

		$query = Connection::getHandle()->prepare("SELECT pagetype AS pagetype, pageid AS pageid FROM bs_page_nicknames WHERE `nickname` = :nickname LIMIT 1");

		$query->bindParam(':nickname', $nickname, PDO::PARAM_STR);

        if ( $query->execute() ) {

            $result = $query->fetch(PDO::FETCH_ASSOC);
            $args[0] = $result['pagetype'];
            $args[1] = (int) $result['pageid'];
        }

		return $args;
	}

	/**
	 * Gets the properties of the page from the database
	 * @param [string]  $type The type of page we are creating, eg: 'category'
	 * @param [integer] $id   The id of the current page.
     * @return Page() current class object
	 */
	private function getProperties($type, $id) {

		if ($this->validateType($type) && $this->validateId($id)) {

            $sql  = "";
			$type = trim(mb_strtolower($type));
			$id   = (int) $id;

			switch ($type) {

				case "page":

					$sql = "SELECT
						        t.pagetype AS type,
						        c.id AS id, c.id AS canonical,
						        n.nickname AS nickname,
						        IF(COUNT(c.id) > 0, TRUE, FALSE) AS validity,
						        c.name AS name,
						        u.url AS short_url,
						        c.secure AS secure,
						        NULL AS slug,
						        c.filename AS filename,
						        c.canonical_page_url,
						        c.title AS title,
						        c.meta_description AS meta_description,
						        c.meta_keywords AS meta_keywords,
						        c.heading AS heading,
						        c.allow_target AS allow_target,
						        c.requires_login AS requires_login,
						        c.disallow_guests AS disallow_guests,
						        c.sitemap_show AS visibility,
						        c.sitemap_page_priority AS priority,
						        c.sitemap_page_change_frequency AS change_frequency
					        FROM bs_pages c
					        JOIN bs_pagetypes t
					        LEFT JOIN bs_page_urls u ON (u.id = c.canonical_page_url AND u.pagetype = t.pagetype AND u.pageid = c.id)
					        LEFT JOIN bs_page_nicknames n ON (t.pagetype = n.pagetype AND c.id = n.pageid)
					        WHERE c.id = :id AND t.pagetype = 'page'
					        GROUP BY c.id";
				break;

				case "landing":

					$sql = "SELECT
						        t.pagetype AS type,
						        l.id AS id,
						        n.nickname AS nickname,
						        IF(COUNT(l.id) > 0, TRUE, FALSE) AS validity,
						        l.name AS name,
						        u.url AS short_url,
						        t.template_secure AS secure,
						        l.slug AS slug,
						        t.template_filename AS filename,
						        l.id AS canonical,
						        l.page_title AS title,
						        l.meta_description AS meta_description,
						        l.meta_keywords AS meta_keywords,
						        l.page_heading AS heading,
						        t.allow_target AS allow_target,
						        t.requires_login AS requires_login,
						        t.disallow_guests AS disallow_guests,
						        l.sitemap_show AS visibility,
						        l.sitemap_page_priority AS priority,
						        l.sitemap_page_change_frequency AS change_frequency
					        FROM bs_landings l
					        JOIN bs_pagetypes t
					        LEFT JOIN bs_page_urls u ON (u.id = l.canonical_page_url AND u.pagetype = t.pagetype AND u.pageid = l.id)
					        LEFT JOIN bs_page_nicknames n ON (t.pagetype = n.pagetype AND l.id = n.pageid)
					        WHERE l.active = TRUE AND l.id = :id AND t.pagetype = 'landing'
					        GROUP BY l.id";
				break;

				case "category":

					$sql = "SELECT
						        t.pagetype AS type,
						        c.id AS id,
						        n.nickname AS nickname,
						        IF(COUNT(c.id) > 0, TRUE, FALSE) AS validity,
						        c.name AS name,
						        u.url AS short_url,
						        t.template_secure AS secure,
						        c.slug AS slug,
						        t.template_filename AS filename,
						        c.id AS canonical,
						        c.page_title AS title,
						        c.meta_description AS meta_description,
						        c.image AS image,
						        c.meta_keywords AS meta_keywords,
						        c.page_heading AS heading,
						        t.allow_target AS allow_target,
						        t.requires_login AS requires_login,
						        t.disallow_guests AS disallow_guests,
						        c.sitemap_show AS visibility,
						        c.sitemap_page_priority AS priority,
						        c.sitemap_page_change_frequency AS change_frequency
					        FROM bs_categories c
					        JOIN bs_pagetypes t
					        LEFT JOIN bs_page_urls u ON (u.id = c.canonical_page_url AND u.pagetype = t.pagetype AND u.pageid = c.id)
					        LEFT JOIN bs_page_nicknames n ON (t.pagetype = n.pagetype AND c.id = n.pageid)
					        WHERE c.active = TRUE AND c.id = :id AND t.pagetype = 'category'
					        GROUP BY c.id";
				break;

				case "grouping":
					$sql = "SELECT
						        t.pagetype AS type,
						        c.id AS id,
						        n.nickname AS nickname,
						        IF(COUNT(c.id) > 0, TRUE, FALSE) AS validity,
						        c.name AS name,
						        u.url AS short_url,
						        t.template_secure AS secure,
						        c.slug AS slug,
						        t.template_filename AS filename,
						        c.id AS canonical,
						        c.page_title AS title,
						        c.meta_description AS meta_description,
						        c.meta_keywords AS meta_keywords,
						        c.page_heading AS heading,
						        t.allow_target AS allow_target,
						        t.requires_login AS requires_login,
						        t.disallow_guests AS disallow_guests,
						        c.sitemap_show AS visibility,
						        c.sitemap_page_priority AS priority,
						        c.sitemap_page_change_frequency AS change_frequency,
						        c.category_id AS parent_id
					        FROM bs_groupings c
					        JOIN bs_pagetypes t
					        LEFT JOIN bs_page_urls u ON (u.id = c.canonical_page_url AND u.pagetype = t.pagetype AND u.pageid = c.id)
					        LEFT JOIN bs_categories cat ON (cat.id = c.category_id)
					        LEFT JOIN bs_page_nicknames n ON (t.pagetype = n.pagetype AND c.id = n.pageid)
					        WHERE cat.active = TRUE AND c.active = TRUE AND c.id = :id AND t.pagetype = 'grouping'
					        GROUP BY c.id";
				break;

				case "subcategory":
					$sql = "SELECT
						        t.pagetype AS type,
						        c.id AS id,
						        n.nickname AS nickname,
						        IF(COUNT(c.id) > 0, TRUE, FALSE) AS validity,
						        c.name AS name,
						        u.url AS short_url,
						        t.template_secure AS secure,
						        c.slug AS slug,
						        t.template_filename AS filename,
						        c.id AS canonical,
						        c.page_title AS title,
						        c.meta_description AS meta_description,
						        c.meta_keywords AS meta_keywords,
						        c.page_heading AS heading,
						        t.allow_target AS allow_target,
						        t.requires_login AS requires_login,
						        t.disallow_guests AS disallow_guests,
						        c.sitemap_show AS visibility,
						        c.sitemap_page_priority AS priority,
						        c.sitemap_page_change_frequency AS change_frequency,
						        grp.id AS parent_id
					        FROM bs_subcategories c
					        JOIN bs_pagetypes t
					        LEFT JOIN bs_page_urls u ON (u.id = c.canonical_page_url AND u.pagetype = t.pagetype AND u.pageid = c.id)
					        LEFT JOIN bs_groupings grp ON (grp.id = c.grouping_id)
					        LEFT JOIN bs_categories cat ON (cat.id = grp.category_id)
					        LEFT JOIN bs_page_nicknames n ON (t.pagetype = n.pagetype AND c.id = n.pageid)
					        WHERE cat.active = TRUE AND grp.active = TRUE AND c.active = TRUE AND c.id = :id AND t.pagetype = 'subcategory'
					        GROUP BY c.id";
				break;

				case "geotarget":
					$sql = "SELECT
						        t.pagetype AS type,
						        c.id AS id,
						        n.nickname AS nickname,
						        IF(COUNT(c.id) > 0, TRUE, FALSE) AS validity,
						        z.zone_name AS name,
						        u.url AS short_url,
						        t.template_secure AS secure,
						        c.slug AS slug,
						        t.template_filename AS filename,
						        c.id AS canonical,
						        c.page_title AS title,
						        c.meta_description AS meta_description,
						        c.meta_keywords AS meta_keywords,
						        c.page_heading AS heading,
						        t.allow_target AS allow_target,
						        t.requires_login AS requires_login,
						        t.disallow_guests AS disallow_guests,
						        c.sitemap_show AS visibility,
						        c.sitemap_page_priority AS priority,
						        c.sitemap_page_change_frequency AS change_frequency,
						        c.subcategory_id AS parent_id
					        FROM bs_subcategories_geotargeted c
					        JOIN bs_pagetypes t
					        LEFT JOIN bs_zones z ON (z.zone_code = c.target)
					        LEFT JOIN bs_page_urls u ON (u.id = c.canonical_page_url AND u.pagetype = t.pagetype AND u.pageid = c.id)
					        LEFT JOIN bs_page_nicknames n ON (t.pagetype = n.pagetype AND c.id = n.pageid)
					        LEFT JOIN bs_subcategories sub ON (sub.id = c.subcategory_id)
					        LEFT JOIN bs_groupings grp ON (grp.id = sub.grouping_id)
					        LEFT JOIN bs_categories cat ON (cat.id = grp.category_id)
					        WHERE cat.active = TRUE AND grp.active = TRUE AND sub.active = TRUE AND c.active = TRUE AND c.id = :id AND t.pagetype = 'geotarget'
					        GROUP BY c.id";
				break;

				case "product":
					$sql = "SELECT
								t.pagetype AS type,
								p.id AS id,
							    n.nickname AS nickname,
								IF(COUNT(p.id) > 0, TRUE, FALSE) AS validity,
								p.default_product_name AS name,
								u.url AS short_url,
								t.template_secure AS secure,
								p.url_slug AS slug,
								t.template_filename AS filename,
								p.page_title AS title,
								p.meta_description AS meta_description,
								p.meta_keywords AS meta_keywords,
								p.page_subtitle AS heading,
								t.allow_target AS allow_target,
								t.requires_login AS requires_login,
								t.disallow_guests AS disallow_guests,
								p.sitemap_show AS visibility,
								p.page_priority AS priority,
								cf.name AS change_frequency
							FROM bs_products p
							JOIN bs_pagetypes t
							LEFT JOIN bs_change_frequencies cf ON (cf.id = p.change_frequency_id AND cf.active = TRUE)
						    LEFT JOIN bs_page_urls u ON (u.id = p.canonical_page_url_id AND u.pagetype = t.pagetype AND u.pageid = p.id)
						    LEFT JOIN bs_page_nicknames n ON (t.pagetype = n.pagetype AND p.id = n.pageid)
							WHERE p.active = TRUE
							AND p.id = :id
						    AND t.pagetype = 'product'
							GROUP BY p.id";
				break;

				case "help":
					$sql = "SELECT
						      t.pagetype AS type,
						      h.id AS id,
						      n.nickname AS nickname,
						      IF(COUNT(h.id) > 0, TRUE, FALSE) AS validity,
						      h.name AS name,
						      u.url AS short_url,
						      t.template_secure AS secure,
						      h.slug AS slug,
						      t.template_filename AS filename,
						      h.id AS canonical,
						      h.page_title AS title,
						      h.meta_description AS meta_description,
						      h.meta_keywords AS meta_keywords,
						      h.page_heading AS heading,
						      t.allow_target AS allow_target,
						      t.requires_login AS requires_login,
						      t.disallow_guests AS disallow_guests,
						      h.sitemap_show AS visibility,
						      h.sitemap_page_priority AS priority,
						      h.sitemap_page_change_frequency AS change_frequency
					        FROM bs_help h
					        JOIN bs_pagetypes t
					        LEFT JOIN bs_page_urls u ON (u.id = h.canonical_page_url AND u.pagetype = t.pagetype AND u.pageid = h.id)
					        LEFT JOIN bs_page_nicknames n ON (t.pagetype = n.pagetype AND h.id = n.pageid)
					        WHERE h.id = :id AND t.pagetype = 'help'
					        GROUP BY h.id";
				break;
			}

			$query = Connection::getHandle()->prepare($sql);

			$query->bindParam(':id', $id, PDO::PARAM_INT);

			if ( $query->execute() ) {

				$result = $query->fetch(PDO::FETCH_ASSOC);

				$this->parentId = isset($result['parent_id']) ? $result['parent_id'] : $result['id'];
				$this->setProperties($result['type'], $result);
			}
		}

		return $this;
	}

	/**
	 * Sets class properties with a supplied data array
     *
     * @param [string] $type
	 * @param [array] $data [array of data to be set as class properties]
     * @return Page() current class object
	 */
	private function setProperties($type, $data) {

		$this->setType($type)
			 ->setId($data["id"])
			 ->setNickname(isset($data["nickname"]) ? $data["nickname"] : NULL)
			 ->setValidity(isset($data["validity"]) ? $data["validity"] : NULL)
			 ->setName(isset($data["name"]) ? $data["name"] : NULL)
			 ->setShortUrl(isset($data["short_url"]) ? $data["short_url"] : NULL)
			 ->setSecure(isset($data["secure"]) ? $data["secure"] : NULL)
			 ->setSlug(isset($data["slug"]) ? $data["slug"] : NULL)
             ->setCanonical(isset($data["canonical"]) ? $data["canonical"] : NULL)
			 ->setFilename(isset($data["filename"]) ? $data["filename"] : NULL)
			 ->setTitle(isset($data["title"]) ? $data["title"] : NULL)
			 ->setMetaDescription(isset($data["meta_description"]) ? $data["meta_description"] : NULL)
			 ->setImage( isset($data["image"]) ? $data["image"] : NULL )
			 ->setMetaKeywords(isset($data["meta_keywords"]) ? $data["meta_keywords"] : NULL)
			 ->setHeading(isset($data["heading"]) ? $data["heading"] : NULL)
			 ->setAllowTarget(isset($data["allow_target"]) ? $data["allow_target"] : NULL)
			 ->setRequiresLogin(isset($data["requires_login"]) ? $data["requires_login"] : NULL)
			 ->setDisallowGuests(isset($data["disallow_guests"]) ? $data["disallow_guests"] : NULL)
			 ->setvisibility(isset($data["visibility"]) ? $data["visibility"] : NULL)
			 ->setPriority(isset($data["priority"]) ? $data["priority"] : NULL)
			 ->setChangeFrequency(isset($data["change_frequency"]) ? $data["change_frequency"] : NULL);

		// Create and set the page URL.
		$this->setUrl($this->createUrl());

		// TODO: UNCOMMENT THIS
		$this->getParentPage();

		return $this;
	}

	/**
	 * Encodes a string in slug format
	 * @param  string $string the string to be encoded to a slug
	 * @return string         the encoded slug
	 */
	private function encodeSlug($string) {

		$string = mb_strtolower($string);
		$string = preg_replace('/[^a-z0-9-_\s]/', '', $string);
		$string = preg_replace('/[-_\s]+/', '-', $string);
		$string = rtrim($string, '-');

		//If a hyphen ends up at the start or end of a slug, remove it
		if ( mb_substr($string, 0, 1) == "-" ) { $string = mb_substr($string, 1, mb_strlen($string)); }
		if ( mb_substr($string, -1, 1) == "-" ) { $string = mb_substr($string, 0, mb_strlen($string)-1); }

		return $string;

	}

	/**
	 * Builds a proper URL based on page type, id, and slug
	 * @return string URL
	 */
	private function createUrl() {

        $url = "";

		if ( isset($this->shortUrl) ) {

			$url = "/" . $this->shortUrl;

		} else {

			switch( $this->type ) {

				case 'category': $url = '/categories/c' . rawurlencode($this->id); break;
				case 'geotarget': $url = '/categories/t' . rawurlencode($this->id); break;
				case 'grouping': $url = '/categories/g' . rawurlencode($this->id); break;
				case 'help': $url = '/help/h' . rawurlencode($this->id); break;
				case 'landing': $url = '/categories/l' . rawurlencode($this->id); break;
				case 'page': $url = str_replace(APP_ROOT, '', $this->filename); break;
				case 'product': $url = '/products/' . rawurlencode($this->id); break;
				case 'subcategory': $url = '/categories/s' . rawurlencode($this->id); break;
			}
                                                                                                    //just a test
			if (!empty($this->slug)) { $url .= "/" . rawurlencode($this->encodeSlug($this->slug)); }
		}

		// return ($this->secure ? URL_PREFIX_HTTPS : URL_PREFIX_HTTP) . $url . (mb_substr($url, -1) !== "/" ? "/" : "");
		return ($this->secure ? URL_PREFIX_HTTPS : URL_PREFIX_HTTP) . $url;
	}

	/**
	 * Check if the current page exists
	 *
	 * @return boolean (returns true if the URL is correct)
	 */
	public function checkUrl() {

		// Get the user's current URL
		$pageURL = (isset($_SERVER["HTTPS"]) && $_SERVER["HTTPS"] == "on" ? "https://" : "http://") . $_SERVER["SERVER_NAME"] . $_SERVER["REQUEST_URI"];

		// Parse the URL, and reform it with the query string omitted
		$parsedURL = parse_url($pageURL);

        if ( $parsedURL['path'] == '/' ) $parsedURL['path'] = str_replace(APP_ROOT, '', $this->filename);

		$constructedURL = $parsedURL['scheme'] . '://' . $parsedURL['host'] . $parsedURL['path'];

		// Compare the user's current URL with the proper constructed URL and see if they match
        return (isset($this->url, $this->requestPath) && $this->url === $constructedURL);
	}

	/**
	 * Returns related groupings/subcategories for any page
	 * @return array [description]
	 */
	public function getRelated() {

        $result = NULL;

		$sql = Connection::getHandle()
                ->prepare("SELECT destination_pagetype AS pagetype, destination_pageid AS pageid
                           FROM bs_related WHERE source_pagetype=? AND source_pageid=? ORDER BY position ASC");

		if ( $sql->execute(array($this->type, $this->getId())) ) {

            while ($row = $sql->fetch(PDO::FETCH_ASSOC)) {

                $result[] = $row;
            }
        }
		return $result;
	}

    /**
     * @param $nickname
     * @return null|string
     */
    public static function getPageUrlFromNickname($nickname) {

		$page = new self($nickname);
		return $page->getUrl();
	}

    /**
     * @param $pageType
     * @param $pageId
     * @return null|string
     */
    public static function getPageUrlFromTypeAndId($pageType, $pageId) {

		$page = new self($pageType, $pageId);

		return $page->getUrl();
	}

    /**
     * @param $pageType
     * @param $pageId
     * @return null|string
     */
    public static function getPageUrl($pageType, $pageId) {

		return isset($pageId) ? self::getPageUrlFromTypeAndId($pageType, $pageId) : self::getPageUrlFromNickname($pageType);
	}

    /**
     * @param $type
     * @param null $id
     * @param null $data
     * @return Page
     */
    public static function create($type, $id = null, $data = null) {
		return new self($type, $id, $data);
	}
}
