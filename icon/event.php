<?
	include "siteList.inc";

	$keyword = $_POST['keyword'];

	if(!$keyword){
		$keyword = $_GET['keyword'];
	}

	$mode = $_GET['mode'];
	if(!$mode)	$mode = "event";

	// 5개 컨텐츠 창중에서 출력하는 창의 순서
	$windowOrder = $_GET['windowOrder'];
	if(!$windowOrder)		$windowOrder = 'window01';


	// event/search 페이지 조회시, 디폴트로 선택하는 카테고리
	$category = $_GET['category'];
	if(!$category)	$category = 'general';

	// event/search 페이지 조회시, 자동 로딩할 윈도우 순서
	if(!$windowOrder){
	        $default = '';
	}else{
        	$default = $windowOrder;
	}


	include "dbconn.inc";


	$eventName = array();
	$eventUrl = array();
	$siteNewWindow = array();
	$siteTop = array();
	$siteHeight = array();


	$result = $db->query("select * from eventSite where category ='{$category}' and mode = '{$mode}' order by ord ASC");

	
	$windowOrd = 1;

	while($row =  mysqli_fetch_object($result)){
		$eventSiteLabel["window0".$windowOrd] = $row->eventSite;
		$eventName["window0".$windowOrd] = $row->eventName;
		$siteTop["window0".$windowOrd] = $row->top;
		$siteHeight["window0".$windowOrd] = $row->height;
		
		if( $mode == 'event'){
			$eventUrl["window0".$windowOrd]  = $row->eventUrl; 
		}else{
			$eventUrl["window0".$windowOrd]  = str_replace("KEYREP",urlencode($keyword),$row->eventUrl);
		}
		$siteNewWindow["window0".$windowOrd] = $row->newWindow;
		$windowOrd++;
	}

?>
<title>모자이크 쇼핑</title>
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">

<link rel="manifest" href="/manifest.json">
<link rel="stylesheet" href="./style.css" type="text/css">
<script  src="https://code.jquery.com/jquery-3.5.1.js"></script>

<script type="text/javascript">

function popOpen() {

    var modalPop = $('.eventMenu');
    var modalBg = $('.eventMenuBg'); 
    var f = document.getElementById(currentSite + '_popup_<?=$category?>');


    $(modalPop).show();
    $(modalBg).show();
    f.style.background = "url('./icon/menu_selected.png') no-repeat";

}

function popClose() {
   var modalPop = $('.eventMenu');
   var modalBg = $('.eventMenuBg');
   var f = document.getElementById(currentSite + '_popup_<?=$category?>');

   $(modalPop).hide();
   $(modalBg).hide();
   f.style.background = "url('') no-repeat";

   return true;
}

currentSite = '';

function view(div_name,ifr_name, site_url){
	
	var f = document.getElementById(div_name);
	var f2 = document.getElementById(ifr_name);
	var f3 = document.getElementById(div_name + '_menu');

	currentSite = div_name;

	hidden();

	f.style.visibility="visible";
	f3.style.background = "url('./icon/menu_selected.png') no-repeat";

	if( f2.src != site_url ){
		
		if( site_url != ""){
			f2.src = site_url;
		}
	}


	return true;	
}


function hidden(){
<?	
	for($m=1;$m < $windowOrd; $m++){
		echo "document.getElementById('window0{$m}').style.visibility = \"hidden\";";
		echo "document.getElementById('window0{$m}_menu').style.background = \"url('') no-repeat\";\r\n";
	}

?>

	return true;
}

function IfmGet(){
	alert( document.getElementById("result").contentWindow.document.location.href);

}

</script>

<style>
	@import url('https://fonts.googleapis.com/css2?family=Nanum+Gothic&display=swap');
</style>

</head>
<body><body>
/*

	상단 공통 검색바

*/
<div id='search_bg'>
<table width='100%'>
	<tr class='dash'>
	<td width='17%'  height='<?=$searchHeight?>'><a href='http://instashopping.co.kr/event.php'><img src='./mosaic-192x192.png' width='<?=$boxHeight?>'></a></td>	
		<td width='17%'></td>
		<td width='17%'></td>
		<td width='17%'></td>
		<td width='17%'></td>
		<td width='17%'><a href='./info.php'><img src='./icon/info.png' width='<?=$boxHeight?>'></a></td>
	</tr>
</table>
</div>
<div id='search_top'>
<form action="./event.php?mode=search&category=<? 
		if($mode == 'search'){ 	echo $category;	}	// 검색 모드일때만, 현재 조회중인 카테고리 유지, 이벤트 모드에서 검색시에는 디폴트 카테고리로 변경
	?>&windowOrder=<?
		if($mode == 'search'){ 	echo $windowOrder;	}	// 검색 모드일때만, 현재 조회중인 카테고리 유지, 이벤트 모드에서 검색시에는 디폴트 카테고리로 변경
	?>" method='post'  autocomplete=on>
	<input type='search' name='keyword' value="<?=$keyword?>" id='search_box'>
	<input type='submit' id='search_button'>
</form>
</div>
<?

/*
 *
 *  컨텐츠 window / iframe 출력  
 *
 */


	echo "<div class='loading'><img width='80px' src='./icon/loading2.gif'></div>\r\n";


reset($siteName);

++$i;

foreach($eventName as $key => $value){

	echo "<div id='{$key}' style='height: ";
	
	if($mode == 'event'){
		echo "100%";
	}else{
		echo $siteHeight[$key]."%";
	}

	echo "; position : fixed; top : ";

	if($mode == 'event'){
		echo "{$searchHeight}";
	}else{
		echo $siteTop[$key];
	}

	echo "px; padding : 0px; width:100%;visibility:hidden; left : 0px; z-index:".++$i.";";


	if(!$siteNewWindow[$key]){
		echo "'><iframe src='' name='{$key}_ifr' id='{$key}_ifr' style=\"width:100%; overflow : hidden; height:";

		echo "100%; position : absolute; padding : 0px; border:none;\" allow=\"clipboard-read; clipboard-write\"></iframe>";
	}else{
		echo "display:flex; align-items:center; justify-content: center; background-color:#FFFFFF;text-align:center' class='alertMsg' >[{$value}]는<BR> 외부 브라우저로 열립니다!<BR><BR><BR><BR><BR><BR><BR>";
	}
	
	echo "</div>\r\n";
}

?>
<div id='foot'>
<table width='100%'>
	<tr class='dash'>
		<td width='17%'  onClick='javascript:popOpen()'>
			<img src='./icon/menu.png' width='<?=$iconSize?>px'>
		</td>

<?

/*
 * 하단 메뉴 출력 
 *
 */


reset($eventName);

$k =0 ;
$autoLoadingCount = 0;
$autoLoading1st = '';
$autoLoading2nd = ' var i = 0; ';

foreach($eventName as $key => $value){

	if(!$default){ // 지정된 사이트가 없으면, 첫번째를 디폴트로 로딩
		$default = $key;
	}else{
		// 디폴트로 지정된 사이트도 아니고, 새창으로 띄우는 사이트도 아니라면, 미리 로딩 대상으로 처리
	 	if($default != $key && !$siteNewWindow[$key] ){ 
			if($autoLoadingCount++ < 1){
				$autoLoading1st .= " document.getElementById('{$key}_ifr').src = '{$eventUrl[$key]}';"; 
			}else{
				// 최초 로딩시, 첫번째 사이트만 로딩하고, 메뉴 클릭 발생시 마다 하나씩만 순서대로 로딩
				$autoLoading2nd .= "if(document.getElementById('{$key}_ifr').src != '{$eventUrl[$key]}' && i < 1 ){  document.getElementById('{$key}_ifr').src = '{$eventUrl[$key]}';  i++; } \r\n";
			}
		}	
	}

	echo "<td width='17%' id='{$key}_menu' height='{$menuHeight}'";
	
		if($siteNewWindow[$key] == "1"){
			echo "> <a href='{$eventUrl[$key]}' target='external' onClick=\"javascript:view('{$key}','{$key}_ifr',''); afterloading();\">";
		}else{
			echo " onClick=\"view('{$key}','{$key}_ifr','{$eventUrl[$key]}'); setTimeout(afterloading,{$autoLoadingDelay});\">\r\n";
		}

	echo "<img class='hover' height='{$iconSize}px' src='./icon/{$eventSiteLabel[$key]}.png'>";
	
	if($siteNewWindow[$key] == "1") echo "</a>";
	echo "</td>";
	$k++;

}

while($k++ < 5){
	echo "<td width='17%' height='{$menuHeight}'>&nbsp;</td>\r\n";
}


?></tr>
</table>
</div>


/*
 
	popup 메뉴 출력 

*/
<div class="eventMenuBg" onClick="javascript:popClose();"></div>
<div class='eventMenu' style='height:<?

	if( $mode == 'event' ){
		echo count($eventCategory)*$menuHeight;
	}else{
		echo "100";
	}

?>px'>
<table width='100%'>
<?

// 메뉴 팝업시, 이벤트 리스트
       $result= $db->query("select * from eventSite where ord <= 5 and mode = '{$mode}' order by category ASC, ord ASC ");

	$cat = null;
	$k = 0;

	while($row = mysqli_fetch_object($result)){

		// 카테고리가 달라졌을때,
		if( $cat != $row->category ){
			// 카테고리 출력이 2번째 이상이고, 카테고리가 달라졌는데, 이전에 5개가 다 출력이 안되었을때,	
			if($cat){
				while($k++ < 5){
					// 이전 카테고리가, 현재 보고 있는 카테고리였는지 확인
					if($category != $preCategory ) echo "<td width='17%' height='{$menuHeight}'>&nbsp;</td>\r\n";
					else $currentCat .= "<td width='17%' height='{$menuHeight}'>&nbsp;</td>\r\n";
				}

				
				if($category != $preCategory )	echo "</tr>\r\n";
				else	$currentCat .= "</tr>\r\n";
			}

			// 새로 시작하는 카테고리가, 현재 보고 있는 카테고리인지?
			if($category != $row->category ){
				echo "<tr class='dash'>\r\n";
				echo "<td width='17%' class='category'  height='{$menuHeight}'>";
        			echo "<a href='./event.php?mode={$mode}&category={$row->category}'>".str_replace(" ","<BR>",$eventCategory[$row->category])."</a>";
        			echo "</td>\r\n";
			}else{ // 현재 보고있는 카테고리는 맨 아래로 출력 
				$currentCat .= "<tr class='dash'>\r\n";
                                $currentCat .= "<td width='17%' class='category' height='{$menuHeight}'";
                                $currentCat .= " onClick='javascript:popClose()'>".str_replace(" ","<BR>",$eventCategory[$row->category]);
				$currentCat .= "</td>\r\n";
			}	
			$k=0;
			$cat = $row->category;
		}

	        // 5개씩 사이트가 출력되는지 체크
		$k++;
		

		if($row->category != $category ){
			echo "<td id='{$row->eventSite}_popup_{$row->category}' width='17%'>";
			if(!$row->newWindow){
				echo " <a href=\"./event.php?mode={$mode}&category={$row->category}&windowOrder=window0{$k}&keyword=".urlencode($keyword)."\">";
			}else{
				echo " <a href=\"{$row->eventUrl}\" target='external' onClick=\"location.href='./event.php?mode={$mode}&category={$row->category}&windowOrder=window0{$k}&keyword=".urlencode($keyword)."';\">";
			}	
			echo "<img class='hover' height='{$iconSize}px' src='./icon/{$row->eventSite}.png'></a>";
                	echo "</td>";

		}else{
			
			$currentCat .= "<td id='{$row->eventSite}_popup_{$row->category}' width='17%'>";
			if( !$row->newWindow ){
				$currentCat .= " <a onClick=\"javascript:popClose();view('window0{$k}','window0{$k}_ifr','{$row->eventUrl}')\">\r\n";
			}else{
				$currentCat .= " <a href=\"{$row->eventUrl}\" target='external' onClick=\"javascript:view('window0{$k}','window0{$k}_ifr','');popClose();\">";
			}	
			$currentCat .= "<img class='hover' height='{$iconSize}px' src='./icon/{$row->eventSite}.png'></a>";
	                $currentCat .= "</td>";
		}

		$preCategory = $row->category; // 이전 카테고리 저장
	}

	// 마지막 출력 카테고리가, 현재 조회중인 카테고리가 아니라면, 
	if($preCategory != $category){
		while($k++ < 5){
        		echo "<td width='17%' height='{$menuHeight}'>&nbsp;</td>\r\n";
		}
	}

	echo $currentCat;

	while($k++ < 5){
                        echo "<td width='17%' height='{$menuHeight}'>&nbsp;</td>\r\n";
        }


?></tr>
</table>

</div>
</body>
<script> 
<?

	if(!$siteNewWindow[$default]){
		 echo "view('{$default}','{$default}_ifr','{$eventUrl[$default]}');";
	}else{
		echo "view('{$default}','{$default}_ifr','');";
	}
	
	// 최초 로딩시, 순서대로 첫번째 1개만 먼저 자동 로딩, 나머지는 고객이 메뉴 눌렀을때마다 1개씩  로딩 
	echo "setTimeout(function(){ {$autoLoading1st} }, {$autoLoadingDelay}); ";

?>

	function afterloading(){
		<?=$autoLoading2nd?>
		// alert(i);
		return true;
	
	}

</script>
