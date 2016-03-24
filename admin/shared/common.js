/*----- 削除時の確認 -----*/
function remove_confirm(){
	if(window.confirm('Are you sure? you are deleteing this parts.')){
		return true;
	}else {
		return false;
	}
}
//ポップアップを開く
function WinOpen(url){
	var win = window.open(url,"_blank");
	win.focus();
}
