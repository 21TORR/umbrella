const {Firefly} = require("@21torr/firefly");

module.exports = (new Firefly())
	.scss({
		umbrella: "assets/scss/umbrella.scss",
	});
