

<!-- PAGE CONTENT BEGINS -->
<input type="hidden" value="{{ $page ?? 1 }}" id="page" /><!--当前页号-->
<input type="hidden" value="{{ $pagesize ?? 20 }}" id="pagesize"/><!--每页显示数量-->
<input type="hidden" value="{{ $total ?? -1 }}" id="total"/><!--总记录数量,小于0重新获取-->
