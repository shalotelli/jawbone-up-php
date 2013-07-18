<?php

/**
 * API Wrapper to access Jawbone UP data using JSON API.
 * 
 *  An official API is supposedly available to partners and in the works. 
 *  
 *  Until that time this method could be used, however there is no guarantee 
 *  on how long this approach will work (UP may disable this ability or change 
 *  their protocol/schema at any time). 
 *  
 *  This information should be used for gaining access to your own data and 
 *  saving for your own use. 
 *  
 *  This should by no means be used in any way that would violate the 
 *  Jawbone UP TOS/AUP.
 *
 * Based on Eric Blue's excellent work, found here:
 * http://eric-blue.com/projects/up-api/
 *
 * The MIT License (MIT)
 * =====================
 * 
 * Copyright (c) 2013, Sha Alibhai
 * All rights reserved.
 * 
 * Permission is hereby granted, free of charge, to any person obtaining a copy of
 * this software and associated documentation files (the "Software"), to deal in
 * the Software without restriction, including without limitation the rights to
 * use, copy, modify, merge, publish, distribute, sublicense, and/or sell copies of
 * the Software, and to permit persons to whom the Software is furnished to do so,
 * subject to the following conditions:
 * 
 * The above copyright notice and this permission notice shall be included in all
 * copies or substantial portions of the Software.
 * 
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY, FITNESS
 * FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE AUTHORS OR
 * COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY, WHETHER
 * IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM, OUT OF OR IN
 * CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE SOFTWARE.
 *
 * @author		Sha Alibhai
 * @link		http://github.com/shalotelli/jawbone-up-php
 * @version 	v1.0.0
 */

class JawboneUp_Exception extends Exception {}

class JawboneUp {

	/**
	 * [$_version description]
	 * 
	 * @var string
	 */
	private $_version = "v1.0.0";

	/**
	 * [$_email description]
	 * 
	 * @var string
	 */
	private $_email = '';

	/**
	 * [$_pass description]
	 * 
	 * @var string
	 */
	private $_pass = '';

	/**
	 * [$_user description]
	 * 
	 * @var [type]
	 */
	private $_user = false;

	/**
	 * [$_token description]
	 * 
	 * @var string
	 */
	private $_token = '';

	/**
	 * [$_uid description]
	 * 
	 * @var string
	 */
	private $_uid = '';

	/**
	 * [$_xid description]
	 * 
	 * @var string
	 */
	private $_xid = '';

	/**
	 * [$_end_point description]
	 * 
	 * @var string
	 */
	private $_end_point = 'https://jawbone.com/';

	/**
	 * [__construct description]
	 * 
	 * @param [type] $email [description]
	 * @param [type] $pass  [description]
	 */
	public function __construct($email, $pass)
	{
		$this->_email = $email;
		$this->_pass = $pass;

		$auth = $this->_authenticate();

		$this->_user = $auth['user'];

		if($this->_user) {
			$this->_user = (array) $this->_user;

			$this->_token = $auth['token'];
			$this->_uid = $auth['user']->uid;
			$this->_xid = $auth['user']->xid;
		}

		return $this;
	}

	/**
	 * [get_version description]
	 * 
	 * @return [type] [description]
	 */
	public function get_version()
	{
		return $this->_version;
	}

	/**
	 * [user_details description]
	 * 
	 * @param  string $attr [description]
	 * 
	 * @return [type]       [description]
	 */
	public function user_details($attr='')
	{
		if(! $this->_user || ! array_key_exists($attr,  $this->_user)) {
			return false;
		}

		return ($attr == '') ? $this->_user : $this->_user[$attr];
	}

	/**
	 * [mail_options description]
	 * 
	 * @param  string $attr [description]
	 * @return [type]       [description]
	 */
	public function mail_options($attr='')
	{
		$info = (array) $this->user_details('mail_opts');

		if($attr == '' || ! array_key_exists($attr, $info)) {
			return $info;
		} else {
			return $info[$attr];
		}
	}

	/**
	 * [smart_alarm description]
	 * 
	 * @param  string $attr [description]
	 * 
	 * @return [type]       [description]
	 */
	public function smart_alarm($attr='')
	{
		$info = (array) $this->user_details('smart_alarm');

		if($attr == '' || ! array_key_exists($attr, $info)) {
			return $info;
		} else {
			return $info[$attr];
		}
	}

	/**
	 * [smart_alarm_enabled description]
	 * 
	 * @return [type] [description]
	 */
	public function smart_alarm_enabled()
	{
		return $this->smart_alarm('enable');
	}

	/**
	 * [smart_alarm_start description]
	 * 
	 * @return [type] [description]
	 */
	public function smart_alarm_start()
	{
		$start_time = $this->smart_alarm('startTimeMinsPastMidnight');
		$midnight = new DateTime('0:00');
		$time = $midnight->add(new DateInterval('PT'.$start_time.'M'));

		return $time->format('g:ia');
	}

	/**
	 * [smart_alarm_stop description]
	 * 
	 * @return [type] [description]
	 */
	public function smart_alarm_stop()
	{
		$stop_time = $this->smart_alarm('stopTimeMinsPastMidnight');
		$midnight = new DateTime('0:00');

		$time = $midnight->add(new DateInterval('PT'.$stop_time.'M'));

		return $time->format('g:ia');
	}

	/**
	 * [first_name description]
	 * 
	 * @return [type] [description]
	 */
	public function first_name()
	{
		return $this->user_details('first_name');
	}

	/**
	 * [last_name description]
	 * 
	 * @return [type] [description]
	 */
	public function last_name()
	{
		return $this->user_details('last_name');
	}

	/**
	 * [name description]
	 * 
	 * @return [type] [description]
	 */
	public function name()
	{
		return $this->user_details('name');
	}

	/**
	 * [band_name description]
	 * 
	 * @return [type] [description]
	 */
	public function band_name()
	{
		return $this->user_details('band_name');
	}

	/**
	 * [email description]
	 * 
	 * @return [type] [description]
	 */
	public function email()
	{
		return $this->user_details('email');
	}

	/**
	 * [image description]
	 * 
	 * @return [type] [description]
	 */
	public function image()
	{
		return $this->user_details('image');
	}

	/**
	 * [profile_privacy description]
	 * 
	 * @return [type] [description]
	 */
	public function profile_privacy()
	{
		return $this->user_details('profile_privacy');
	}

	/**
	 * [member_since description]
	 * 
	 * @param  string $format [description]
	 * @return [type]         [description]
	 */
	public function member_since($format='m/d/y g:ia')
	{
		return date($format, $this->user_details('up_member_since'));
	}

	/**
	 * [apps description]
	 * 
	 * @return [type] [description]
	 */
	public function apps()
	{
		return $this->user_details('apps');
	}

	/**
	 * [share_move description]
	 * 
	 * @return [type] [description]
	 */
	public function share_move()
	{
		return $this->user_details('share_move');
	}

	/**
	 * [share_sleep description]
	 * 
	 * @return [type] [description]
	 */
	public function share_sleep()
	{
		return $this->user_details('share_sleep');
	}

	/**
	 * [share_eat description]
	 * 
	 * @return [type] [description]
	 */
	public function share_eat()
	{
		return $this->user_details('share_eat');
	}

	/**
	 * [share_mood description]
	 * 
	 * @return [type] [description]
	 */
	public function share_mood()
	{
		return $this->user_details('share_mood');
	}

	/**
	 * [primary_address description]
	 * 
	 * @return [type] [description]
	 */
	public function primary_address()
	{
		return $this->user_details('primary_address');
	}

	/**
	 * [basic_info description]
	 * 
	 * @param  string $attr [description]
	 * 
	 * @return [type]       [description]
	 */
	public function basic_info($attr='')
	{
		$info = (array) $this->user_details('basic_info');

		if($attr == '' || ! array_key_exists($attr, $info)) {
			return $info;
		} else {
			return $info[$attr];
		}
	}

	/**
	 * [weight description]
	 * 
	 * @return [type] [description]
	 */
	public function weight()
	{
		return $this->basic_info('weight');
	}

	/**
	 * [dob description]
	 * 
	 * @return [type] [description]
	 */
	public function dob()
	{
		return $this->basic_info('dob');
	}

	/**
	 * [gender description]
	 * 
	 * @return [type] [description]
	 */
	public function gender()
	{
		return $this->basic_info('gender');
	}

	/**
	 * [metric description]
	 * 
	 * @return [type] [description]
	 */
	public function metric()
	{
		return $this->basic_info('metric');
	}

	/**
	 * [height description]
	 * 
	 * @return [type] [description]
	 */
	public function height()
	{
		return $this->basic_info('height');
	}

	/**
	 * [locale description]
	 * 
	 * @return [type] [description]
	 */
	public function locale()
	{
		return $this->basic_info('locale');
	}

	/**
	 * [goals description]
	 * 
	 * @param  string $attr [description]
	 * @return [type]       [description]
	 */
	public function goals($attr='')
	{
		$info = (array) $this->user_details('goals');

		if($attr == '' || ! array_key_exists($attr, $info)) {
			return $info;
		} else {
			return $info[$attr];
		}
	}

	/**
	 * [move_goal description]
	 * 
	 * @return [type] [description]
	 */
	public function move_goal()
	{
		return $this->goals('move');
	}

	/**
	 * [sleep_goal description]
	 * 
	 * @return [type] [description]
	 */
	public function sleep_goal()
	{
		return $this->goals('sleep');
	}

	/**
	 * [eat_goal description]
	 * 
	 * @return [type] [description]
	 */
	public function eat_goal()
	{
		return $this->goals('eat');
	}

	/**
	 * [up_goals description]
	 * 
	 * @param  string $attr [description]
	 * @return [type]       [description]
	 */
	public function up_goals($attr='')
	{
		$info = (array) $this->user_details('up_goals');

		if($attr == '' || ! array_key_exists($attr, $info)) {
			return $info;
		} else {
			return $info[$attr];
		}
	}

	/**
	 * [up_move_goals description]
	 * 
	 * @return [type] [description]
	 */
	public function up_move_goals()
	{
		return $this->up_goals('move');
	}

	/**
	 * [up_sleep_goals description]
	 * 
	 * @return [type] [description]
	 */
	public function up_sleep_goals()
	{
		return $this->up_goals('sleep');
	}

	/**
	 * [up_meal_goals description]
	 * 
	 * @return array
	 */
	public function up_meal_goals()
	{
		return $this->up_goals('meals');
	}

	/**
	 * [power_nap description]
	 * 
	 * @param  string $attr [description]
	 * @return [type]       [description]
	 */
	public function power_nap($attr='')
	{
		$info = (array) $this->user_details('power_nap');

		if($attr == '' || ! array_key_exists($attr, $info)) {
			return $info;
		} else {
			return $info[$attr];
		}
	}

	/**
	 * [optimal_power_nap description]
	 * 
	 * @return [type] [description]
	 */
	public function optimal_power_nap()
	{
		return $this->power_nap('use_optimal_duration');
	}

	/**
	 * [power_nap_custom_duration description]
	 * 
	 * @return [type] [description]
	 */
	public function power_nap_custom_duration()
	{
		return ($this->power_nap('custom_duration')/60);
	}

	/**
	 * [power_nap_max_duration description]
	 * 
	 * @return [type] [description]
	 */
	public function power_nap_max_duration()
	{
		return ($this->power_nap('maximum_duration')/60);
	}

	/**
	 * [active_alert description]
	 * 
	 * @param  string $attr [description]
	 * @return [type]       [description]
	 */
	public function active_alert($attr='')
	{
		$info = (array) $this->user_details('active_alert');

		if($attr == '' || ! array_key_exists($attr, $info)) {
			return $info;
		} else {
			return $info[$attr];
		}
	}

	/**
	 * [active_alert_start description]
	 * 
	 * @return [type] [description]
	 */
	public function active_alert_start()
	{
		$start_time = $this->active_alert('startTimeMinsPastMidnight');
		$midnight = new DateTime('0:00');
		$time = $midnight->add(new DateInterval('PT'.$start_time.'M'));

		return $time->format('g:ia');
	}

	/**
	 * [active_alert_stop description]
	 * 
	 * @return [type] [description]
	 */
	public function active_alert_stop()
	{
		$stop_time = $this->active_alert('stopTimeMinsPastMidnight');
		$midnight = new DateTime('0:00');
		$time = $midnight->add(new DateInterval('PT'.$stop_time.'M'));

		return $time->format('g:ia');
	}

	/**
	 * [active_alert_duration description]
	 * 
	 * @return [type] [description]
	 */
	public function active_alert_duration()
	{
		return $this->active_alert('durationMins');
	}

	/**
	 * [active_alert_threshold description]
	 * 
	 * @return [type] [description]
	 */
	public function active_alert_threshold()
	{
		return $this->active_alert('threshold');
	}

	/**
	 * [active_alert_type description]
	 * 
	 * @return [type] [description]
	 */
	public function active_alert_type()
	{
		return $this->active_alert('type');
	}

	/**
	 * [token description]
	 * 
	 * @return [type] [description]
	 */
	public function token()
	{
		return $this->_token;
	}

	/**
	 * [uid description]
	 * 
	 * @return [type] [description]
	 */
	public function uid()
	{
		return $this->_uid;
	}

	/**
	 * [xid description]
	 * 
	 * @return [type] [description]
	 */
	public function xid()
	{
		return $this->_xid;
	}

	/**
	 * Feed Summary
	 *
	 * The following request is issued each time the Feed tab is viewed. 
	 * This tab contains high-level info and a graphic for your Workouts, 
	 * Sleep, and Food.
	 * 
	 * @return [type] [description]
	 */
	public function feed_summary()
	{
		$args = 
			array(
				'after' => null, 
				'limit' => 30
			);

		return $this->_request('nudge/api/users/@me/social', $args);
	}

	/**
	 * Daily Summary (Activity + Sleep + Food) Data
	 *
	 * The following request is issued when the Me tab is selected 
	 * (the Today screen).
	 * 
	 * @return [type] [description]
	 */
	public function daily_summary()
	{
		;
	}

	/**
	 * Detailed Activity Data
	 * 
	 * The following request is issued when an activity/workout graph 
	 * is viewed. Intervals are 60 seconds apart. Note that a given 
	 * profile ID can also be substituted with @me
	 * 
	 * @return [type] [description]
	 */
	public function detailed_activity()
	{
		;
	}

	/**
	 * Sleep Summary Data
	 * 
	 * The following request is issued when an sleep graph is viewed. 
	 * Note that the xid returned for the sleep data can be used to 
	 * get more detailed data (snapshot)
	 * 
	 * @return [type] [description]
	 */
	public function sleep_summary()
	{
		;
	}

	/**
	 * Sleep Detailed Data (Snapshot)
	 * 
	 * The following request is issued when an sleep graph is viewed. 
	 * Each sleep data entry is added whenever a state change happens 
	 * (1, 2 or 3):
	 * 
	 * 1 = awake
	 * 2 = deep
	 * 3 = light
	 * 
	 * @return [type] [description]
	 */
	public function detailed_sleep()
	{
		;
	}

	/**
	 * Workout Summary Data
	 * 
	 * The following request is issued when a workout graph is viewed. 
	 * Note that the xid returned for the workout data can be used to 
	 * get more detailed data (snapshot)
	 * 
	 * @return [type] [description]
	 */
	public function workout_summary()
	{
		;
	}

	/**
	 * Workout Detailed Data (Snapshot)
	 * 
	 * The following request is issued when an sleep graph is viewed. 
	 * Each sleep data entry is added whenever a state change happens 
	 * (1, 2 or 3):
	 * 
	 * The value for each item returned appears to be estimated 
	 * calories burned.
	 * 
	 * @return [type] [description]
	 */
	public function detailed_workout()
	{
		;
	}

	/**
	 * Authentication
	 * 
	 * Each API request needs to be validated and requires that a secure 
	 * token be passed. This token can be retrieved by performing a login 
	 * request.
	 * 
	 * @return [type]        [description]
	 */
	private function _authenticate()
	{
		$args = 
			array(
				'email' => $this->_email, 
				'pwd' => $this->_pass, 
				'service' => 'nudge'
			);

		return $this->_request('user/signin/login', $args, false);
	}

	/**
	 * Work horse. Every API call use this function to actually make the 
	 * request to Jawbones servers.
	 * 
	 * @param  string $method API method name
	 * @param  array  $args   query arguments
	 * @param  string $http   GET or POST request type
	 * 
	 * @return array|string|JawboneUp_Exception
	 */
	private function _request($method, $args=array(), $auth=true, $http='POST')
	{
		if($auth) {
			// $auth = $this->_authenticate();
			// $this->_token = $auth['token'];
			$args['_token'] = $this->_token;
		}

		$url = $this->_end_point.$method;

		switch($http) {
			case 'GET':
				// some distributions change arg sep to &amp; by default
				$separator_changed = false;

				if(init_get("arg_separator.output")!='&') {
					$separator_changed = true;
					$original_separator = ini_get('arg_separator.output');
					ini_set('arg_separator.output', '&');
				}

				$url = '?'.http_build_query($args);

				if($separator_changed) {
					ini_set('arg_separator.output', $original_separator);
				}

				$response = $this->_http_request($url, array(), 'GET');
			break;

			case 'POST':
				$response = $this->_http_request($url, $args, 'POST');
			break;

			default:
				throw new JawboneUp_Exception('Unknown request type');
		}

		$response_code = $response['header']['http_code'];
		$response_body = $response['body'];

		if($response_code == 200) {
			return $response_body;
		} else {
			print_r($response_body);exit;
			$message = isset($body['error']->msg) ? $body['error']->msg : '';

			throw new JawboneUp_Exception($message, $response_code);
		}
	}

	/**
	 * [_http_request description]
	 * 
	 * @param  [type] $url    [description]
	 * @param  array  $fields [description]
	 * @param  string $method [description]
	 * 
	 * @return [type]         [description]
	 */
	private function _http_request($url, $fields=array(), $method='POST')
	{
		if(! in_array($method, array('GET', 'POST'))) {
			$method = 'POST';
		}

		// some distributions change arg sep to &amp; by default
		$separator_changed = false;

		if(ini_get("arg_separator.output")!='&') {
			$separator_changed = true;
			$original_separator = ini_get('arg_separator.output');
			ini_set('arg_separator.output', '&');
		}

		$fields = is_array($fields) ? http_build_query($fields) : $fields;

		if($separator_changed) {
			ini_set('arg_separator.output', $original_separator);
		}

		if(function_exists('curl_init') && function_exists('curl_exec')) {
			if(! ini_get('safe_mode')) {
				set_time_limit(2 * 60);
			}

			$headers = array(
				'User-Agent' => "Nudge/2.5.6 CFNetwork/609.1.4 Darwin/13.0.0", 
				'Accept' => 'application/json',
        		'x-nudge-platform' => 'iPhone 5,2; 6.1.3',
        		'Accept-Encoding' => 'plain'
			);

			if($this->_token!=='') {
				$headers['x-nudge-token'] = $this->_token;
			}

			$ch = curl_init();

			curl_setopt($ch, CURLOPT_URL, $url);
			curl_setopt($ch, CURLOPT_POST, ($method=='POST'));
			curl_setopt($ch, CURLOPT_POSTFIELDS, $fields);
			curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
			curl_setopt($ch, CURLOPT_HEADER, false);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
			curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 2 * 60 * 1000);

			$response = curl_exec($ch);
			$info 	  = curl_getinfo($ch);
			$error 	  = curl_error($ch);

			curl_close($ch);
		} elseif(function_exists('fsockopen')) {
			$parsed_url = parse_url($url);

			$host = $parsed_url['host'];

			if(isset($parsed_url['path'])) {
				$path = $parsed_url['path'];
			} else {
				$path = '/';
			}

			$params = '';

			if(isset($parsed_url['query'])) {
				$params = $parsed_url['query'].'&'.$fields;
			} elseif(trim($fields)!='') {
				$params = $fields;
			}

			if(isset($parsed_url['port'])) {
				$port = $parsed_url['port'];
			} else {
				$port = ($parsed_url['scheme']=='https') ? 443 : 80;
			}

			$response = false;

			$errno  = '';
			$errstr = '';

			ob_start();

			$fp = fsockopen('ssl://'.$host, $port, $errno, $errstr, 5);

			if($fp) {
				stream_set_timeout($fp, 30);

				$payload =  "$method $path HTTP/1.0\r\n" .
							"Host: $host\r\n" . 
							"Connection: close\r\n"  .
							"Content-type: application/x-www-form-urlencoded\r\n" .
							"Content-length: " . strlen($params) . "\r\n" .
							"Connection: close\r\n\r\n" .
							$params;

				fwrite($fp, $payload);
				stream_set_timeout($fp, 30);

				$info = stream_get_meta_data($fp);

				while((! feof($fp)) && (! $info["timed_out"])) {
					$response .= fread($fp, 4096);
					$info = stream_get_meta_data($fp);
				}

				fclose($fp);

				ob_end_clean();

				list($headers, $response) = explode("\r\n\r\n", $response, 2);

				if(ini_get("magic_quotes_runtime")) {
					$response = stripslashes($response);
				}

				$info = array('http_code' => 200);
			} else {
				ob_end_clean();
				$info = array('http_code' => 500);
				throw new Exception($errstr, $errno);
			}

			$error = '';
		} else {
			throw new JawboneUp_Exception('No valid HTTP transport found', -99);
		}

		return 
			array(
				'header' => $info,
				'body' 	 => (array) json_decode($response),
				'error'  => $error
			);
	}

}