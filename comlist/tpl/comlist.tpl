<!-- BEGIN: MAIN -->
<div id="comlist">

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

<!-- IF {PAGE_ROW_COMLIST_IS_NUMERIC} -->
			<a href="{PAGE_ROW_URL}" class="fw-bold mt-1 d-block">{PAGE_ROW_SHORTTITLE}</a>
<!-- ELSE -->
			<a href="{PAGE_ROW_CAT_URL}" class="fw-bold mt-1 d-block">{PAGE_ROW_CAT_TITLE}</a>
<!-- ENDIF -->

			<div class="text small lh-sm mb-1">
				{PAGE_ROW_COMLIST_TEXT_PLAIN}
			</div>
			<p class="text-end small m-0">
				{PAGE_ROW_COMLIST_AUTHORNAME} / {PAGE_ROW_COMLIST_DATE}
			</p>
		</li>
<!-- END: PAGE_ROW -->
	</ul>

<!-- IF {PAGE_TOP_PAGINATION} -->
	<nav class="mt-1" aria-label="Sample Pagination">
		<ul class="pagination pagination-sm justify-content-left m-0">
			{PAGE_TOP_PAGEPREV}{PAGE_TOP_PAGINATION}{PAGE_TOP_PAGENEXT}
		</ul>
	</nav>
<!-- ENDIF -->

</div>
<!-- END: MAIN -->
