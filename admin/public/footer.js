(function() {
	document.write("<!-- 前端模板部分 -->");
	document.write("<!-- 列表模板部分 开始  <! -- 模板中可以用HTML注释 -- >  或  <%* 这是模板自带注释格式 *%> -->");
	document.write("<script type=\"text\/template\"  id=\"baidu_template_sms_params_list\">");
	document.write("    <%for(var i = 0; i<data_list.length;i++){");
	document.write("    var item = data_list[i];");
	document.write("    %>");
	document.write("    <tr>");
	document.write("        <td><%=item.param_name%><\/td>");
	document.write("        <td><%=item.param_code%><input type=\"hidden\" name=\"param_code[]\" value=\"<%=item.param_code%>\"><\/td>");
	document.write("        <td><%=item.param_type_text%><input type=\"hidden\" name=\"param_type[]\" value=\"<%=item.param_type%>\"><\/td>");
	document.write("        <td>");
	document.write("            <%if( (item.param_type == 4) || (item.param_type == 8 && SMS_OPERATE_TYPE == 2) ){%>");
	document.write("            <input type=\"text\" name=\"param_val[]\" value=\"\"  placeholder=\"请输入内容\"  style=\"width:100px; \">");
	document.write("            <%}else{%>");
	document.write("            <input type=\"text\" name=\"param_val[]\" value=\"\"  placeholder=\"请输入内容\"  style=\"width:100px;display: none; \">");
	document.write("            <%}%>");
	document.write("        <\/td>");
	document.write("        <td><%=item.date_time_format%><\/td>");
	document.write("        <td><%=item.fixed_val%><\/td>");
	document.write("    <\/tr>");
	document.write("    <%");
	document.write("    }%>");
	document.write("<\/script>");
	document.write("<!-- 列表模板部分 结束-->");
	document.write("<!-- 前端模板结束 -->");
}).call();