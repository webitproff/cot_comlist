# cot_comlist
Rendering comment widgets anywhere

## Использование:

```html
{PHP|cot_comlist($tpl, $items, $order, $blacklist, $whitelist, $group, $userid)}
```

Назначение параметров (в скобках значение по умолчанию -- если не указано пользователем):
* $tpl -- имя шаблона (по умолчанию comlist)
* $items -- количество выводимых записей (0 -- вывести все)
* $order -- сортировка (date -- по дате, views -- по просмотрам, rand -- рандомизировать)
* $blacklist -- "черный" список
* $whitelist -- "белый" список
* $group -- сгруппировать по страницам
* $userid -- только по id пользователя

## В шаблоне генерятс следующие теги:

* {PAGE_ROW_COMLIST_NUM} -- порядковый номер
* {PAGE_ROW_COMLIST_ODDEVEN} -- класс odd/even
* {PAGE_ROW_COMLIST_ID} -- id комментария
* {PAGE_ROW_COMLIST_URL} -- ссылка на комментарий
* {PAGE_ROW_COMLIST_AUTHOR} -- кликабельное имя автора
* {PAGE_ROW_COMLIST_AUTHORNAME} -- имя автора
* {PAGE_ROW_COMLIST_AUTHORID} -- пользовательский id автора
* {PAGE_ROW_COMLIST_TEXT} -- текст комментария
* {PAGE_ROW_COMLIST_TEXT_PLAIN} -- текст комментария, обработанный функцией strip_tags (без NULL-байтов, PHP- и HTML-тегов)
* {PAGE_ROW_COMLIST_DATE} -- дата в формате Cotonti
* {PAGE_ROW_COMLIST_DATE_STAMP} -- дата в формате timestamp

### Подключение тегов PAGE_ROW_

Кроме основных тегов плагин генерит весьнабор тегов для родительской страницы (функция cot_generate_usertags)


## История:

вер. 1.10
1. Плагин переименован из pagecom в comlist
2. Мелкие исправления и улучшения

===

## How to Use:
