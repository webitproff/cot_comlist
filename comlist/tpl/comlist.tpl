<!-- BEGIN: MAIN -->
	<ul class="list-unstyled comments">
<!-- BEGIN: PAGE_ROW -->
		<li class="px-3 py-2 mb-3 {PAGE_ROW_ODDEVEN}">

			<figure class="me-3 mb-0 float-start">
<!-- IF {PAGE_ROW_AUTHOR_AVATAR} -->
				{PAGE_ROW_AUTHOR_AVATAR}
<!-- ELSE -->
				<img src="datas/defaultav/default.png" alt="{PAGE_ROW_AUTHORNAME}" class="img-fluid" />
<!-- ENDIF -->
			</figure>

<!-- IF {PAGE_ROW_AREA} == 'page' -->
<!-- IF {PAGE_ROW_CODE_IS_NUMERIC} -->
			<a href="{PAGE_ROW_PAGE_URL}" class="fw-bold mb-2 d-block">{PAGE_ROW_PAGE_SHORTTITLE}</a>
<!-- ELSE -->
			<a href="{PAGE_ROW_CAT_URL}" class="fw-bold mb-2 d-block">{PAGE_ROW_CAT_TITLE}</a>
<!-- ENDIF -->
<!-- ENDIF -->

<!-- IF {PAGE_ROW_AREA} == 'polls' -->
			<a href="{PAGE_ROW_POLL_URL}" class="fw-bold mb-2 d-block">{PAGE_ROW_POLL_TITLE}</a>
<!-- ENDIF -->

			<div class="text small lh-sm mb-2">
				{PAGE_ROW_TEXT_PLAIN}
			</div>

			<p class="text-end small mb-0">
				{PAGE_ROW_AUTHORNAME} / {PAGE_ROW_DATE}
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
