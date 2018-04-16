//表单字段验证
//验证字段,验证规则,错误提示,附加规则
function tcheck(string,rule,info,addrules) {
	//string(字段),rule(规则),info提示信息,addrules(附加规则),smin(最小值),smax(最大值)
	//判断是否为空
	if (rule == '' || rule == 'require') {
		if (string == '') {
			wintq(info,2,2000,1,'');
			return false;
		}else {
			return true;
		}
	}
	//判断是否为数字
	if (rule == 'number') {
		if (!/^[0-9]{0,20}$/.test(string)) {
			wintq(info,2,1000,1,'');
			return false;
		}else {
			return true;
		}
	}
	//判断长度
	if (addrules == 'length') {
		var arr = rule.split(',');
		if (string.length < arr[0] || string.length > arr[1]) {
			wintq(info,2,1000,1,'');
			return false;
		}else {
			return true;
		}
	}
	//验证两个字段值是否相同
	if (addrules == 'equal') {
		if (string != rule) {
			wintq(info,2,1000,1,'');
			return false;
		}else {
			return true;
		}
	}
	//正则表达式判断
	if (addrules == 'regexp') {
		if (!rule.test(string)) {
			wintq(info,2,1000,1,'');
			return false;
		}else {
			return true;
		}
	}
	//验证邮箱
	if (rule == 'email') {
		if (!/^([a-zA-Z0-9_\.-]+)@([\da-zDA-Z0-9\.-]+)\.([a-zA-Z0-9\.]{2,6})$/.test(string)) {
			wintq(info,2,1000,1,'');
			return false;
		}else {
			return true;
		}
	}
	//验证URL地址
	if (rule == 'url') {
		if (!/^(https?:\/\/)?([\da-z\.-]+)\.([a-z\.]{2,6})([\/\w \.-]*)*\/?$/.test(string)) {
			wintq(info,2,1000,1,'');
			return false;
		}else {
			return true;
		}
	}
	//验证IP地址
	if (rule == 'ip') {
		if (!/^(?:(?:25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)\.){3}(?:25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)$/.test(string)) {
			wintq(info,2,1000,1,'');
			return false;
		}else {
			return true;
		}
	}
	//验证用户名，包含中文
	if (rule == 'username') {
		if (!/^[a-zA-Z0-9_-]|[\u4e00-\u9fa5]{2,16}$/.test(string)) {
			wintq(info,2,1000,1,'');
			return false;
		}else {
			return true;
		}
	}
}