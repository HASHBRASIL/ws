$(document).ready(function() {

	var handleTimePickers = function() {

		if (jQuery().timepicker) {
			$('.timepicker-24').timepicker({
				minuteStep : 1,
				showSeconds : true,
				showMeridian : false,
				defaultTime: false
			});
		}
	};
	
	handleTimePickers();

});