if (window.location.host === "localhost" || window.location.host === "127.0.0.1")
{
	var base_url = window.location.origin+ window.location.pathname;
}
else{
	var base_url = window.location.origin+ '/';
	// const base_url = "https://smartschoolautomation.com/";
}