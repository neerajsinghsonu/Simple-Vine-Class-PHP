<?php
if (! defined ( "INSITE" )) die ( "No direct access allowed!" );
	
	// check if session not start then start the session
! session_id () ? session_start () : '';
/**
 * Class Vine
 *
 * Simple Vine Class Using Auth Process
 * and Fetch Data using CURL
 *
 * @author neeraj.singh
 *        
 */
class Vine {
	
	/**
	 * Vine User Login (Email || Name)
	 *
	 * @var string
	 * @access protected
	 */
	protected $loginName = '';
	
	/**
	 * Vine User Login Password
	 *
	 * @var string
	 * @access protected
	 */
	protected $loginPassword = '';
	
	/**
	 * Vine Key
	 *
	 * @var string
	 * @access protected
	 */
	protected $key = null;
	
	/**
	 * Vine User ID
	 *
	 * @var string
	 * @access protected
	 */
	protected $userId = null;
	
	/**
	 * Vine User Name
	 *
	 * @var string
	 * @access protected
	 */
	protected $userName = null;
	
	/**
	 * Vine User Image Link
	 *
	 * @var string
	 * @access protected
	 */
	protected $avatarUrl = null;
	
	/**
	 * Vine Auth URL
	 *
	 * @var string
	 */
	protected $authUrl = 'https://api.vineapp.com/users/authenticate';
	/**
	 * Get user basic details
	 * and set data in class property
	 *
	 * @return mixed
	 */
	public function __construct() {
		if (! function_exists ( 'curl_version' )) {
			die ( 'PHP cURL is required for this class.' );
		}
		$fields_string = '';
		$fields = array (
				'username' => urlencode ( addslashes ( $this->loginName ) ),
				'password' => urlencode ( addslashes ( $this->loginPassword ) ) 
		);
		foreach ( $fields as $key => $value ) {
			$fields_string .= $key . '=' . $value . '&';
		}
		// trim the & sign
		rtrim ( $fields_string, '&' );
		// create a new cURL resource
		$ch = curl_init ();
		// set URL and other appropriate options
		curl_setopt ( $ch, CURLOPT_URL, $this->authUrl );
		curl_setopt ( $ch, CURLOPT_HEADER, false );
		curl_setopt ( $ch, CURLOPT_SSL_VERIFYHOST, 0 );
		curl_setopt ( $ch, CURLOPT_SSL_VERIFYPEER, false );
		curl_setopt ( $ch, CURLOPT_POST, count ( $fields ) );
		curl_setopt ( $ch, CURLOPT_POSTFIELDS, $fields_string );
		curl_setopt ( $ch, CURLOPT_RETURNTRANSFER, 1 );
		curl_setopt ( $ch, CURLOPT_FOLLOWLOCATION, 1 );
		// grab URL and pass it to the browser
		$result = curl_exec ( $ch );
		// decode the data in json
		$ret = json_decode ( $result );
		// if return success
		if ($ret->success) {
			// set calss variable
			$_SESSION ['key'] = $this->key = $ret->data->key;
			$this->userId = $ret->data->userId;
			$this->userName = $ret->data->username;
			$this->avatarUrl = $ret->data->avatarUrl;
		}
		// close cURL resource, and free up system resources
		curl_close ( $ch );
		// return the data
		return $ret;
	}
	/**
	 * Get the Vine Data for Requested Tag
	 *
	 * By Default Vine returns 19 data
	 *
	 * @param string $tag        	
	 * @param number $page        	
	 * @param number $size        	
	 */
	public function searchTag($tag = 'hollywood', $page = 1, $size = 11) {
		$data = array ();
		if (! empty ( $this->key ) && ! empty ( $tag )) {
			$header = array (
					'user-agent: com.vine.iphone/1.0.3 (unknown, iPhone OS 6.1.0, iPhone, Scale/2.000000)',
					'vine-session-id: ' . $this->key,
					'accept-language: en, sv, fr, de, ja, nl, it, es, pt, pt-PT, da, fi, nb, ko, zh-Hans, zh-Hant, ru, pl, tr, uk, ar, hr, cs, el, he, ro, sk, th, id, ms, en-GB, ca, hu, vi, en-us;q=0.8' 
			);
			// start the CURL
			$ch = curl_init ();
			// set auth URL
			$authUrl = "https://api.vineapp.com/timelines/tags/" . $tag . '?page=' . $page . '&size=' . $size;
			// set URL and other appropriate options
			curl_setopt ( $ch, CURLOPT_URL, $authUrl );
			curl_setopt ( $ch, CURLOPT_SSL_VERIFYHOST, 0 );
			curl_setopt ( $ch, CURLOPT_SSL_VERIFYPEER, false );
			curl_setopt ( $ch, CURLOPT_RETURNTRANSFER, 1 );
			curl_setopt ( $ch, CURLOPT_FOLLOWLOCATION, 1 );
			curl_setopt ( $ch, CURLOPT_HTTPHEADER, $header );
			// execute the CURL
			$result = curl_exec ( $ch );
			// convert json in php array
			$ret = json_decode ( $result );
		} else {
			die ( 'Ha!Ha!! You missed the tag' );
		}
		// data more than 0 then start parsing array
		if (isset ( $ret->data->count ) && ($ret->data->count > 0)) {
			// parsing the return data
			foreach ( $ret->data->records as $i => $a ) {
				// get user liked or not current feed
				$data [$i] ['liked'] = $ret->data->records [$i]->liked;
				// how many other users likes this
				$data [$i] ['likes'] ['count'] = $ret->data->records [$i]->likes->count;
				
				// now get the user details who liked the current feed
				if ($data [$i] ['likes'] ['count'] > 0) {
					foreach ( $ret->data->records [$i]->likes->records as $j => $b ) {
						// get likes user details
						$data [$i] ['likes'] ['data'] [$j] ['user_name'] = $ret->data->records [$i]->likes->records [$j]->username;
						$data [$i] ['likes'] ['data'] [$j] ['user_id'] = $ret->data->records [$i]->likes->records [$j]->userId;
					}
				}
				
				// get the current feed thumbnail url
				$data [$i] ['thumbnail_url'] = $ret->data->records [$i]->thumbnailUrl;
				// get the current feed avatar url
				$data [$i] ['avatar_url'] = $ret->data->records [$i]->avatarUrl;
				// get the number of total comments on current feed
				$data [$i] ['comments'] ['count'] = $ret->data->records [$i]->comments->count;
				
				// now get the user details who comment on the current feed
				if ($data [$i] ['comments'] ['count'] > 0) {
					foreach ( $ret->data->records [$i]->comments->records as $k => $c ) {
						// get user details who make comments on current feed
						$data [$i] ['comments'] ['data'] [$k] ['comment'] = $ret->data->records [$i]->comments->records [$k]->comment;
						$data [$i] ['comments'] ['data'] [$k] ['user_name'] = $ret->data->records [$i]->comments->records [$k]->user->username;
						$data [$i] ['comments'] ['data'] [$k] ['user_id'] = $ret->data->records [$i]->comments->records [$k]->user->userId;
						$data [$i] ['comments'] ['data'] [$k] ['description'] = $ret->data->records [$i]->comments->records [$k]->user->description;
						$data [$i] ['comments'] ['data'] [$k] ['avatar_url'] = $ret->data->records [$i]->comments->records [$k]->user->avatarUrl;
						$data [$i] ['comments'] ['data'] [$k] ['location'] = $ret->data->records [$i]->comments->records [$k]->user->location;
					}
				}
				
				// now get the entities if has more than 0
				if (count ( $ret->data->records [$i]->entities ) > 0) {
					foreach ( $ret->data->records [$i]->entities as $l => $d ) {
						$data [$i] ['entities'] ['data'] [$l] ['link'] = $ret->data->records [$i]->entities [$l]->link;
						$data [$i] ['entities'] ['data'] [$l] ['type'] = $ret->data->records [$i]->entities [$l]->type;
						$data [$i] ['entities'] ['data'] [$l] ['title'] = $ret->data->records [$i]->entities [$l]->title;
					}
				}
				
				// now get the real media/video
				$data [$i] ['media'] ['data'] ['low_video_url'] = $ret->data->records [$i]->videoLowURL;
				$data [$i] ['media'] ['data'] ['permalink_url'] = $ret->data->records [$i]->permalinkUrl;
				$data [$i] ['media'] ['data'] ['user_name'] = $ret->data->records [$i]->username;
				$data [$i] ['media'] ['data'] ['description'] = $ret->data->records [$i]->description;
				$data [$i] ['media'] ['data'] ['high_video_url'] = $ret->data->records [$i]->videoUrl;
				$data [$i] ['media'] ['data'] ['created_date'] = $ret->data->records [$i]->created;
			}
			
			// get pagineation information
			$data ['next'] = $ret->data->nextPage;
			$data ['prev'] = (! empty ( $ret->data->previousPage )) ? $ret->data->previousPage : 0;
			$data ['size'] = $ret->data->size;
			
			return $data;
		}
	}
}
