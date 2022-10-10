<?
namespace lib\usertype;

use Bitrix\Main\Localization\Loc,
    Bitrix\Main\Loader,
	Bitrix\Iblock;

Loc::loadMessages(__FILE__);

class CUserTypePropEList
{
    const USER_TYPE = 'myEList';

    public function GetUserTypeDescription()
    {
        return array(
            'USER_TYPE' => self::USER_TYPE,
            'CLASS_NAME' => __CLASS__,
            'DESCRIPTION' => 'Список связанных элементов с чекбоксами',
            'PROPERTY_TYPE' => Iblock\PropertyTable::TYPE_ELEMENT,
            'ConvertToDB' => [__CLASS__, 'ConvertToDB'],
            'ConvertFromDB' => [__CLASS__, 'ConvertFromDB'],
            'GetPropertyFieldHtml' => [__CLASS__, 'GetPropertyFieldHtml'],
        );
    }

    /**
     * Конвертация данных перед сохранением в БД
     * @param $arProperty
     * @param $value
     * @return mixed
     */

    public static function ConvertToDB($arProperty, $value)
    {
        if ($value['VALUE']['TIME_FROM'] != '' && $value['VALUE']['TIME_TO']!='')
        {
            try {
                $value['VALUE'] = base64_encode(serialize($value['VALUE']));
            } catch(Bitrix\Main\ObjectException $exception) {
                echo $exception->getMessage();
            }
        } else {
            $value['VALUE'] = '';
        }

        return $value;
    }

    /**
     * Представление формы редактирования значения
     * @param $arUserField
     * @param $arHtmlControl
     */
    public static function GetPropertyFieldHtml($arProperty, $value, $arHtmlControl)
    {
        $weekDays = [
            'mon' => 'Понедельник',
            'tue' => 'Вторник',
            'wed' => 'Среда',
            'thu' => 'Четверг',
            'fri' => 'Пятница',
            'sat' => 'Суббота',
            'sun' => 'Воскресенье',
        ];

        $itemId = 'row_' . substr(md5($arHtmlControl['VALUE']), 0, 10); //ID для js
        $fieldName =  htmlspecialcharsbx($arHtmlControl['VALUE']);
        //htmlspecialcharsback нужен для того, чтобы избавиться от многобайтовых символов из-за которых не работает unserialize()
        $arValue = unserialize(htmlspecialcharsback($value['VALUE']), [stdClass::class]);

        $select = '<select class="week_day" name="'. $fieldName .'[WEEK_DAY]">';
        foreach ($weekDays as $key => $day){
            if($arValue['WEEK_DAY'] == $key){
                $select .= '<option value="'. $key .'" selected="selected">'. $day .'</option>';
            } else {
                $select .= '<option value="'. $key .'">'. $day .'</option>';
            }

        }
        $select .= '</select>';

        $html = '<div class="property_row" id="'. $itemId .'">';

        $html .= '<div class="reception_time">';
        $html .= $select;
        $timeFrom = ($arValue['TIME_FROM']) ? $arValue['TIME_FROM'] : '';
        $timeTo = ($arValue['TIME_TO']) ? $arValue['TIME_TO'] : '';

        $html .='&nbsp;время приёма: с&nbsp;<input type="time" name="'. $fieldName .'[TIME_FROM]" value="'. $timeFrom . '">';
        $html .='&nbsp;по&nbsp;<input type="time" name="'. $fieldName .'[TIME_TO]" value="'. $timeTo .'">';
        if($timeFrom!='' && $timeTo!=''){
            $html .= '&nbsp;&nbsp;<input type="button" style="height: auto;" value="x" title="Удалить" onclick="document.getElementById(\''. $itemId .'\').parentNode.parentNode.remove()" />';
        }
        $html .= '</div>';

        $html .= '</div><br/>';

        return $html;
    }
}