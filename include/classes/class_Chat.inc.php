<?php

/**
 * Chat is a class for determining the availability of our live chat operators.
 *
 * Example usage:
 *
 *     $chat = new Chat();
 *     $chatStatus = $chat->getStatuws();
 *
 * @uses APP_ROOT and CHAT_STATUS_URL constants from config.php.
 * @author Ken Xu
 */


class Chat {

     const CACHE_DIR = 'cache';
     const CACHE_FILENAME = 'chatstatus.json';

     private $cacheFile;
     private $status;

   /**
    * Instantiate the object and import the current status from the cache.
    *
    * @return self
    */
     public function __construct() {

         // Define the cache file path.
         $this->cacheFile = APP_ROOT.'/'.self::CACHE_DIR.'/'.self::CACHE_FILENAME;

         // Import the cache data.
         $this->importCache();

     }

   /**
    * Provide public access to the private `status` property.
    *
    * @return self
    */
     public function getStatus() {
         return $this->status;
     }

   /**
    * Import the chat status from the cache.
    *
    * @return self
    */
     private function importCache() {

         // Read the cache and parse the JSON.
         if( is_readable($this->cacheFile) ) {

             $this->status = json_decode(file_get_contents($this->cacheFile), true);
         }
         return $this;

     }

   /**
    * Request an updated status from the chat service provider and cache the parsed results.
    *
    * Since this requires an HTTP request to a third party, it can be slow. It's thus
    * recommended to use this function only from cron jobs or other scripts that aren't
    * time-sensitive.
    *
    * @return self
    */
     public function updateCache() {

         // Prepare the status array with default values.
         $this->status = array(
             'company' => false,
             'operators' => array(),
             'departments' => array()
         );

         // Ask ProvideSupport for the status.
         $curlHandle = curl_init(CHAT_STATUS_URL);
         curl_setopt($curlHandle, CURLOPT_RETURNTRANSFER, true);
         $rawStatus = curl_exec($curlHandle);

         // If a response was received...
         if ( $rawStatus !== false ) {

             // Look at the response line by line.
             foreach ( preg_split("/\r\n|\n|\r/", $rawStatus) as $rawStatusLine ) {

                 // Prepare an array for the regular expression mapping.
                 $matches = array();

                 // If the line is in one of the understood formats...
                 if ( preg_match('/^(COMPANY|OPERATOR|DEPARTMENT) "?([^"]+)"? : (.+)$/', $rawStatusLine, $matches) ) {

                     // Update the status array.
                     if ( $matches[1] === 'COMPANY' ) {
                         $this->status['company'] = ($matches[3] === 'ONLINE');
                     } elseif ( $matches[1] === 'OPERATOR' ) {
                         $this->status['operators'][$matches[2]] = ($matches[3] === 'ONLINE');
                     } elseif ( $matches[1] === 'DEPARTMENT' ) {
                         $this->status['departments'][$matches[2]] = ($matches[3] === 'ONLINE');
                     }

                 }

             }

         }

         // Cache the status array as JSON.
         file_put_contents($this->cacheFile, json_encode($this->status));

         return $this;

     }
 }
