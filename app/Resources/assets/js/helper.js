function number_format (number, decimals, decPoint, thousandsSep, custom) {

    number = (number + '').replace(/[^0-9+\-Ee.]/g, '');
    var n = !isFinite(+number) ? 0 : +number;
    var prec = !isFinite(+decimals) ? 0 : Math.abs(decimals);
    var sep = (typeof thousandsSep === 'undefined') ? ',' : thousandsSep;
    var dec = (typeof decPoint === 'undefined') ? '.' : decPoint;
    var customFormat = (typeof custom === 'undefined');

    var toFixedFix = function (n, prec) {
        var k = Math.pow(10, prec);
        return '' + (Math.round(n * k) / k)
                .toFixed(prec);
    };

    // @todo: for IE parseFloat(0.55).toFixed(0) = 0;
    var s = (prec ? toFixedFix(n, prec) : '' + Math.round(n)).split('.');

    var part1 = '';
    var part2 = '';

    if (s[0].length > 3) {
        if (customFormat) {
            part1 = s[0].substring(s[0].length - 3, s[0].length);
            part2 = s[0].substring(0, s[0].length - 3);
            part2 = part2.replace(/\B(?=(?:\d{2})+(?!\d))/g, sep);
            s[0] = part2 + sep + part1;
        } else {
            s[0] = s[0].replace(/\B(?=(?:\d{3})+(?!\d))/g, sep)
        }
    }

    if ((s[1] || '').length < prec) {
        s[1] = s[1] || '';
        s[1] += new Array(prec - s[1].length + 1).join('0')
    }

    return s.join(dec)
}

function custom_number_format(val) {
    return number_format(val, 2, '.', ',');
}

function sanitize_amount(val) {
    return parseFloat(banglaToEnglish(val).replace(/,/g, '')) || 0;
}

function blockUI(el) {
    App.blockUI({
        target: el,
        animate: true,
        overlayColor: 'black'
    });
}

function unblockUI(el) {
    App.unblockUI(el);
}

var en2bnMap = {'1':'১', '2':'২', '3':'৩', '4':'৪', '5':'৫', '6':'৬', '7':'৭', '8':'৮', '9':'৯', '0': '০' };
var bn2enMap = {'১':'1', '২':'2', '৩':'3', '৪':'4', '৫': '5', '৬':'6' , '৭':'7', '৮':'8', '৯':'9', '০':'0' };

function replacemap(input, digitMap) {

    var replacechars = function(c){
        return digitMap[c] || c;
    };

    return input.split('').map(replacechars).join('');
}

function banglaToEnglish(input)
{
    return replacemap(input, bn2enMap);
}

function englishToBanglaDigit(input)
{
    return replacemap(input, en2bnMap);
}
