<?php

	namespace Hasokeyk\SanalSantralAPI;

	use Hasokeyk\SanalSantralAPI\SMS\SMS;

	class SanalSantral{

		public function SMS($sms_api_key = null, $sms_title = null, $sms_packet_id = null){
			return new SMS($sms_api_key, $sms_title, $sms_packet_id);
		}

	}