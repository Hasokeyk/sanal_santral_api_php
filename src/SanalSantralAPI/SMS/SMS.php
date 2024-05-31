<?php

	namespace Hasokeyk\SanalSantralAPI\SMS;

	use Hasokeyk\SanalSantralAPI\SanalSantralRequest;

	//DÖKÜMAN : https://sms2.sanalsantral.com.tr/docs/SMS_Gateway_API_V3.pdf?v=3

	class SMS{

		private $sms_api_url = 'https://sms2.sanalsantral.com.tr/api'; //https://sms2.sanalsantral.com.tr/api/smspost/v1
		private $sms_api_version = 'v1';
		private $sms_api_key;
		private $sms_title;
		private $sms_packet_id;

		private $request;

		function __construct($sms_api_key, $sms_title, $sms_packet_id){
			$this->sms_title     = $sms_title;
			$this->sms_packet_id = $sms_packet_id;
			$this->sms_api_key   = $sms_api_key;
			$this->request       = new SanalSantralRequest();
		}

		public function get_sms_credit(){

			$url      = $this->get_api_link('credit');
			$response = $this->request->get($url);
			if(isset($response['status']) and $response['status'] == 200){
				$body   = $response['body'] ?? null;
				$result = $this->get_result_code($body, 'credit');
				return $result;
			}

			return $response;

		}

		public function get_sms_titles(){

			$url      = $this->get_api_link('baslik');
			$response = $this->request->get($url);
			if(isset($response['status']) and $response['status'] == 200){
				$body   = $response['body'] ?? null;
				$result = $this->get_result_code($body);

				$titles           = json_decode($body);
				$result['titles'] = $titles ?? null;

				return $result;
			}

			return $response;

		}

		public function send_sms($number = null, $message = null, $type = '1'){

			$url = $this->get_api_link('smspost');

			$sms_body_xml = '<sms>'."\n";
			$sms_body_xml .= '<apikey>'.$this->sms_api_key.'</apikey>'."\n";
			$sms_body_xml .= '<header>'.$this->sms_title.'</header>'."\n";
			$sms_body_xml .= '<type></type>'."\n";
			$sms_body_xml .= '<validity>2880</validity>'."\n";
			$sms_body_xml .= '<ticari>0</ticari>'."\n";
			$sms_body_xml .= '<message>'."\n";
			$sms_body_xml .= '<gsm>'."\n";
			$sms_body_xml .= '<no>'.$number.'</no>'."\n";
			$sms_body_xml .= '</gsm>'."\n";
			$sms_body_xml .= '<msg><![CDATA['.$message.']]></msg>'."\n";
			$sms_body_xml .= '</message>'."\n";
			$sms_body_xml .= '</sms>';

			$headers = [
				'Content-Type'     => 'text/xml; charset=utf-8',
				'Content-Encoding' => 'UTF-8',
			];

			$response = $this->request->post($url, $sms_body_xml, $headers, 'body');
			if(isset($response['status']) and $response['status'] == 200){
				$body   = $response['body'] ?? null;
				$result = $this->get_result_code($body, 'sms_id');
				return $result;
			}

			return $response;

		}

		public function get_sms_status($sms_id = null){

			$url_param = [
				'id' => $sms_id
			];
			$url       = $this->get_api_link('dlr', $url_param);
			$response  = $this->request->get($url);
			if(isset($response['status']) and $response['status'] == 200){
				$body   = $response['body'] ?? null;
				$result = $this->get_result_code($body, 'credit');
				return $result;
			}

			return $response;

		}

		public function get_api_link($end_point = '', $params = null){
			$url_query = [
				'apikey' => $this->sms_api_key,
			];
			$url_query = $url_query + $params;
			return $this->sms_api_url = $this->sms_api_url.'/'.$end_point.'/'.$this->sms_api_version.'?'.http_build_query($url_query);
		}

		public function get_result_code($body = '0', $result_name = 'id'){
			preg_match('|(\d{2})(\s)?(\d+)?|is', $body, $get_code);
			$code = $get_code[1] ?? '0';
			$id   = $get_code[3] ?? null;

			$codes = [
				'0'  => [
					'code_text'    => 'SUCCESS',
					'code_message' => 'İşlem başarılı',
				],
				'00' => [
					'code_text'    => 'SUCCESS',
					'code_message' => 'İşlem başarılı',
				],
				'99' => [
					'code_text'    => 'UNKNOWN_ERROR',
					'code_message' => 'Bilinmeyen hata',
				],
				'97' => [
					'code_text'    => 'USE_POST_METHOD',
					'code_message' => 'POST ile yollayınız',
				],
				'95' => [
					'code_text'    => 'USE_GET_METHOD',
					'code_message' => 'GET ile yollayınız',
				],
				'93' => [
					'code_text'    => 'MISSING_GET_PARAMS',
					'code_message' => 'GET değeri boş',
				],
				'91' => [
					'code_text'    => 'MISSING_POST_DATA',
					'code_message' => 'POST değeri boş',
				],
				'89' => [
					'code_text'    => 'WRONG_XML_FORMAT',
					'code_message' => 'XML parse edilemedi',
				],
				'87' => [
					'code_text'    => 'WRONG_USER_OR_PASSWORD',
					'code_message' => 'Kullanıcı şifre hatalı',
				],
				'83' => [
					'code_text'    => 'EMPTY_SMS',
					'code_message' => 'Mesaj metni boş',
				],
				'81' => [
					'code_text'    => 'NOT_ENOUGH_CREDITS',
					'code_message' => 'Kredi yetersiz',
				],
				'79' => [
					'code_text'    => 'DLR_ID_NOT_FOUND',
					'code_message' => 'Sms id bulunamadı',
				],
				'29' => [
					'code_text'    => 'MESSAGE_WAITING_TO_SEND',
					'code_message' => 'Mesaj gönderilmek üzere beklemede',
				],
				'27' => [
					'code_text'    => 'MESSAGE_SEND_ERROR',
					'code_message' => 'Mesaj gönderilirken hata oluştu',
				],
				'25' => [
					'code_text'    => 'DLR_OPERATION_STARTED',
					'code_message' => 'DLR raporu güncellemeye başlamış',
				],
				'23' => [
					'code_text'    => 'DLR_OPERATION_COMPLETED',
					'code_message' => 'SMS Gönderilmiş.',
				],
			];

			$result             = $codes[$code] ?? [];
			$result['response'] = $body;
			if(isset($id)){
				$result[$result_name] = $id;
			}
			return $result;
		}
	}