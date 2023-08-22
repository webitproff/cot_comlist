[Plugin] Comlist / Pagecom

Плагин вывода комментариев через функцию по условиям (аргументам):

    $tpl – имя шаблона
    $items – количество выводимых элементов (при непустом $pagination – на страницу)
    $order – сортировка в формате SQL (по умолчанию com_id DESC)
    $extra – дополнительный SQL-запрос
    $group – значение 1 группирует комментарии по страницам
    $pagination – код паджинации
    $ajax_block – id блока при использовании аякса (включение аякса в конфиге плагина)
    $cache_name – имя записи кэша
    $cache_ttl – срок действия кэша

Примеры использования

// Вывести 5 последних комментариев
`{PHP|cot_comlist(‘comlist’, 5}`
 
// Вывести 5 последних комментариев, сгруппированных по страницам

`{PHP|cot_comlist(‘comlist’, 5, ‘’, ‘’, 1}`
 
// Вывести последние комментарии, сгруппированные по страницам 
// и с паджинацией по 5 записей на страницу

`{PHP|cot_comlist(‘comlist’, 5, ‘’, ‘’, 1, ‘compage’}`
 
// Вывести последние комментарии, сгруппированные по страницам 
// и с ajax-паджинацией по 5 записей на страницу
`{PHP|cot_comlist(‘comlist’, 5, ‘’, ‘’, 1, ‘compage’, ‘com2list’}`
 
// Вывести 5 последних комментариев к опросам

`{PHP|cot_comlist(‘comlist’, 5, '', 'com_area = "polls"'}`
 
// Вывести 5 последних комментариев к разделу docs

`{PHP|cot_comlist(‘comlist’, 5, '', 'com_area = "page" and com_code = "docs"'}`

Почему удалены аргументы black / white lists

Imho, гораздо проще генерить список страниц или разделов отдельным плагином или указывать его в ресурсном файле, после чего кэшировать. Пример использования:

	
// Создаем черный список страниц категории system

`if (Cot::$cache && Cot::$cache->db->exists('comments_blacklist', 'comlist')) {
  $comments_blacklist = Cot::$cache->db->get('comments_blacklist', 'comlist');
}
else {
  $verb = "SELECT page_id FROM " . Cot::$db->pages . " WHERE page_cat = 'system'";
  $comments_blacklist = Cot::$db->query($verb)->fetchAll(PDO::FETCH_COLUMN);
  $comments_blacklist = '(' . implode(',', $comments_blacklist) . ')';
  Cot::$cache && Cot::$cache->db->store('comments_blacklist', $comments_blacklist, 'comlist', 604800);
}`


и тогда в шаблоне:

`{PHP.comments_blacklist|cot_comlist('comlist', '5', 'com_date DESC', 'com_code NOT IN $this')}`
