<!-- BEGIN: MAIN -->
<ul class="list-unstyled comments">
<!-- BEGIN: PAGE_ROW -->
	<li class="{PAGE_ROW_ODDEVEN} px-3 py-2 overflow-hidden">
		<figure class="me-3 mb-1 float-start">
			{PAGE_ROW_USER_AVATAR}
		</figure>

<!-- IF {PAGE_ROW_AREA} == 'page' -->
<!-- IF {PAGE_ROW_CODE_IS_NUMERIC} -->
		<a href="{PAGE_ROW_PAGE_URL}#c{PAGE_ROW_ID}" class="lh-1 fw-bold mb-1 d-block">{PAGE_ROW_PAGE_SHORTTITLE}</a>
<!-- ELSE -->
		<a href="{PAGE_ROW_CAT_URL}#c{PAGE_ROW_ID}" class="lh-1 fw-bold mb-1 d-block">{PAGE_ROW_CAT_TITLE}</a>
<!-- ENDIF -->
<!-- ENDIF -->

<!-- IF {PAGE_ROW_AREA} == 'polls' -->
		<a href="{PAGE_ROW_POLL_URL}#c{PAGE_ROW_ID}" class="lh-1 fw-bold mb-1 d-block">{PAGE_ROW_POLL_TITLE}</a>
<!-- ENDIF -->

		<div class="text small lh-sm mb-2">
			{PAGE_ROW_TEXT_PLAIN|cot_cutstring($this, '160')}
		</div>

		<p class="text-end small mb-0">
			{PAGE_ROW_AUTHORNAME} @ {PAGE_ROW_DATE}
		</p>

	</li>
<!-- END: PAGE_ROW -->
</ul>

<!-- IF {PAGE_TOP_PAGINATION} -->
<nav aria-label="Comlist Pagination">
	<ul class="pagination pagination-sm justify-content-center mb-0">
		{PAGE_TOP_PAGEPREV}{PAGE_TOP_PAGINATION}{PAGE_TOP_PAGENEXT}
	</ul>
</nav>
<!-- ENDIF -->
<!-- END: MAIN -->
