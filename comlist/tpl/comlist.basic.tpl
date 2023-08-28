<!-- BEGIN: MAIN -->
	<ul class="list-unstyled comlist">
<!-- BEGIN: PAGE_ROW -->
		<li class="{PAGE_ROW_COMLIST_ODDEVEN} overflow-hidden">

			<figure class="mb-0">
<!-- IF {PAGE_ROW_AUTHOR_AVATAR} -->
				{PAGE_ROW_AUTHOR_AVATAR}
<!-- ELSE -->
				<img src="datas/defaultav/default.png" alt="{PAGE_ROW_COMLIST_AUTHORNAME}" class="img-fluid" />
<!-- ENDIF -->
			</figure>

			<div class="text">
				{PAGE_ROW_COMLIST_TEXT_PLAIN}
			</div>

			<p>
				{PAGE_ROW_COMLIST_AUTHORNAME} / {PAGE_ROW_COMLIST_DATE}
			</p>

		</li>
<!-- END: PAGE_ROW -->
	</ul>

<!-- IF {PAGE_TOP_PAGINATION} -->
	<nav aria-label="Comlist Pagination">
		<ul class="pagination justify-content-center mb-0">
			{PAGE_TOP_PAGEPREV}{PAGE_TOP_PAGINATION}{PAGE_TOP_PAGENEXT}
		</ul>
	</nav>
<!-- ENDIF -->
<!-- END: MAIN -->
