# cot_comlist
Генератор списков комментариев

## Использование:

```html
{PHP|cot_comlist($tpl, $items, $order, $extra, $group, $offset, $pagination, $ajax_block, $cache_name, $cache_ttl)}
```

Назначение параметров:
* $tpl -- имя шаблона (по умолчанию comlist)
* $items -- количество выводимых записей (по умолчанию 0 -- вывести все)
* $order -- сортировка (по умолчанию "com_date DESC")
* $extra -- дополнительное MYSQL-условие
* $group -- сгруппировать по страницам
* $offset -- сдвиг на указанное количество записей от первой
* $pagination -- код паджинации
* $ajax_block -- ajax-блок (id), по умолчанию comlist
* $cache_name -- имя записи кэша
* $cache_ttl -- срок жизни кэша

## В шаблоне генерятся следующие теги:

* {PAGE_ROW_NUM} -- порядковый номер
* {PAGE_ROW_ODDEVEN} -- класс odd/even (чет/нечет)
* {PAGE_ROW_COMLIST_IS_NUMERIC} -- цифровой (не символьный) код 'com_code'
* {PAGE_ROW_COMLIST_ID} -- id комментария
* {PAGE_ROW_COMLIST_CODE} -- код комментария
* {PAGE_ROW_COMLIST_AREA} -- расширение комментария
* {PAGE_ROW_COMLIST_AUTHOR} -- кликабельное имя автора
* {PAGE_ROW_COMLIST_AUTHORNAME} -- имя автора
* {PAGE_ROW_COMLIST_AUTHORID} -- пользовательский id автора
* {PAGE_ROW_COMLIST_AUTHORIP} -- ip автора
* {PAGE_ROW_COMLIST_TEXT} -- текст комментария
* {PAGE_ROW_COMLIST_TEXT_PLAIN} -- текст комментария, обработанный функцией strip_tags (без NULL-байтов, PHP- и HTML-тегов)
* {PAGE_ROW_COMLIST_DATE} -- дата в формате Cotonti
* {PAGE_ROW_COMLIST_DATE_STAMP} -- дата в формате timestamp

### Подключение тегов PAGE_ROW_

Кроме основных тегов плагин генерит весь набор тегов для родительской страницы (функция cot_generate_pagetags)

### Подключение тегов PAGE_ROW_AUTHOR_

Кроме основных тегов плагин может генерить весь набор тегов для владельца комментария (функция cot_generate_usertags)

## История:

вер. 1.20
1. Удалены параметры черного и белого списка
2. Добавлено кэширование (только при отключенных аяксе и паджинации)
3. Добавлена работа с аяксом
4. Добавлена индикация новых комментариев

вер. 1.10
1. Плагин переименован из pagecom в comlist
2. Мелкие исправления и улучшения

===

## How to Use:
