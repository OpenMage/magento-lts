
function showSearchForm(a)
{
	Effect.SlideDown('topSearch', {scaleContent:false, duration:0.3});
	if(a > 0){
		$('search').value = '';
		$('search').focus();
	}
	return false;
}