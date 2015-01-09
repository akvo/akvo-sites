var Kwg_Timer = {

	aTimers: {},

	setTimeout: function (sId, mFunction, iDelay, aArguments) {
		var oThis = this;

		oThis.aTimers[sId] = window.setTimeout(mFunction, iDelay);

	},

	clearTimeout: function (sId) {
		var oThis = this;

		window.clearTimeout(oThis.aTimers[sId]);
	},

	setInterval: function (sId, mFunction, iInterval, aArguments) {
		var oThis = this;

		oThis.aTimers[sId] = window.setInterval(mFunction, iInterval);
	},

	clearInterval: function (sId) {
		var oThis = this;

		window.clearInterval(oThis.aTimers[sId]);
	}

};