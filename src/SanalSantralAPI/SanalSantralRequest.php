<?php

	namespace Hasokeyk\SanalSantralAPI;

	use GuzzleHttp\Client;
	use GuzzleHttp\Exception\GuzzleException;

	class SanalSantralRequest{

		private $client;
		private $api_url = 'https://api.sanal.link/api/';

		function __construct(){

			$this->client = new Client([
				'verify'      => false,
				'version'     => 2.0,
				'http_errors' => false
			]);

		}

		private function default_header(){

			return [
				'Accept' => 'application/json',
				//				'Content-type' => 'application/json',
			];

		}

		public function get($url = null, $headers = null, $post_type = 'form'){

			try{

				$headers = $headers ?? $this->default_header();
				$options = [
					'headers' => $headers,
					'curl'    => [
						CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_2_0,
					]
				];

				$request = $this->client->get($url, $options);

				return [
					'status'  => $request->getStatusCode(),
					'body'    => $request->getBody()->getContents(),
					'headers' => $request->getHeaders(),
				];

			}catch(GuzzleException $exception){
				return [
					'status'  => 'fail',
					'message' => $exception->getMessage() ?? 'Empty',
				];
			}


		}

		public function post($url = null, $post_data = null, $headers = null, $post_type = 'form'){

			try{

				$headers = $headers ?? $this->default_header();
				$options = [
					'headers' => $headers,
					'curl'    => [
						CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_2_0,
					]
				];

				if($post_type == 'form'){
					$options['form_params'] = ($post_data ?? null);
				}
				else if($post_type == 'raw_json'){
					$options['body'] = json_encode($post_data);
				}
				else if($post_type == 'body'){
					$options['body'] = ($post_data);
				}

				$request = $this->client->post($url, $options);

				return [
					'status'  => $request->getStatusCode(),
					'body'    => $request->getBody()->getContents(),
					'headers' => $request->getHeaders(),
				];

			}catch(GuzzleException $exception){
				return [
					'status'  => 'fail',
					'message' => $exception->getMessage() ?? 'Empty',
				];
			}


		}

	}