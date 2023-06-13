<!-- BEGIN: MAIN -->
<ul class="list-unstyled comments">
<!-- BEGIN: PAGE_ROW -->
	<li class="px-3 py-2 mb-3 {PAGE_ROW_COMLIST_ODDEVEN}">
		<figure class="m-0">
<!-- IF {PAGE_ROW_AUTHOR_AVATAR} -->
			{PAGE_ROW_AUTHOR_AVATAR}
<!-- ELSE -->
			<img src="datas/defaultav/blank.png" alt="{PAGE_ROW_COMLIST_AUTHORNAME}" class="img-fluid" />
<!-- ENDIF -->
		</figure>
		<a href="{PAGE_ROW_URL}">{PAGE_ROW_SHORTTITLE}</a>
		<div class="text lh-sm mb-1">
			{PAGE_ROW_COMLIST_TEXT_PLAIN}
		</div>
		<p class="text-end small m-0">
			{PAGE_ROW_COMLIST_AUTHORNAME} / {PAGE_ROW_COMLIST_DATE}
		</p>
	</li>
<!-- END: PAGE_ROW -->
</ul>
<!-- END: MAIN -->
