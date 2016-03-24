<?php

	/**
	 * Class used for featured pages (old landing page)
	 */
	class FeaturedPage
	{
		/**
		* @return Array: Returns data queried from the database.
		**/
		public static function fetchData()
		{

			$results = array ();

			//Connect to db and prepare our query
			$query =  Connection::getHandle()->prepare (
				"SELECT pagetype,
						pageid,
						header,
						snippet,
						image,
						position
				FROM bs_featured_pages order by position ASC");

			$query->execute ();
			$data = $query->fetchAll (PDO::FETCH_OBJ);

			if (!empty ($data) && sizeof ($data) > 0)
			{
				//Iterate over our data and push $url in to the object
				foreach ($data as $k => $o)
				{
					if (!empty ($o->pageid) && !empty ($o->pagetype))
					{
						$url = Page::getPageUrl ($o->pagetype, $o->pageid);
						$results [$k] = $o;
						$results [$k]->pageUrl = $url;
					}
				}
			}
			return $results;
		}
	}