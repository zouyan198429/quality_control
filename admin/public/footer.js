(function() {
	document.write("<!-- 列列显示文件列表模板部分 开始-->");
	document.write("<!-- 对数对象格式：{upload_id:\'上传对象的id-必填\', upload_url:\'上传文件接口地址[可为空]\'} -->");
	document.write("<script type=\"text\/template\"  id=\"baidu_template_upload_file_show\">");
	document.write("    <div class=\"resourceBlock\">");
	document.write("        <div class=\"cards upload_img uploader-files file-list file-list-grid file-rename-by-click\">");
	document.write("        <\/div>");
	document.write("        <span id=\'<%=upload_id%>\' class=\"uploader\" data-ride=\"uploader\" data-url=\"<%=upload_url%>\" style=\"display: none;\">");
	document.write("        <\/span>");
	document.write("    <\/div>");
	document.write("<\/script>");
	document.write("<!-- 列列显示文件列表模板部分 结束-->");
}).call();