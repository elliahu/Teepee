# Teepee
Evidenční systém oddílu Royal Rangers

![Teepee beta logo](/view/img/teepee-text-logo-dark.png)

V případě dotazu nebo zájmu o celý zdrojový kód mě můžete kontaktovat na [matejelias.cz](https://matejelias.cz/)

## O Teepee
Aplikace vznikla na základě potřeby modernizace způsobu zápisu docházky a celkové evidence oddílu. Doposud jsme vše v oddílu zapisovali na papíry, které se ztrácely. Teepee nabízí efektivní způsob evidence docházky, účasti na akcích a moho dalšího.

### Co Teepee umí :fire:
- Evidence účasti na schůzkách (přítomen, neřítomen, omluven)
- Evidence účasti na víkendových akcích
- Statistické údaje pro celý oodíl
- Statistické údaje pro jednotlivé kmeny
- Odesílání hromadných html emailů rodičum
- Grafické znázorněné veškerých statistik
- Jednoduché a výkonné API přiojení pro vlastní projetky (zatím ve fázi testování)

### Co Teepee nabízí
- Super Mail Manager
- Jednoduché API
- Vlastní error handling system
- Jednoduchou rozšiřitelnost

# Dokumentace :page_facing_up:
Dokumentace slouží k pochopená základního principu aplikace, časem ji budu rozšiřovat.

## Jak to vlastně funguje
Aplikace je psaná v prehistorickém ale jednoduchém jazyku PHP a je postavená na dobře známe [MVC architektuře](https://cs.wikipedia.org/wiki/Model-view-controller).
### Život aplikace
1. Všechno to začne v souboru `index.php`, kde se vytvoří instance **routeru**.
2. **Router** převezme parametry z URL adresy a vytvoří instanci příslušného kontroleru
3. Řízení se předá tomuto kontroleru
### Důležité soubory
- V souboru `db_config.php` se nachází nastavení připojení k databázi
- Práci s databází má nastarost třída `model/DB.php`
- V souboru `init.php`, který z bezpečnostních důvodů není přítomen v repozitáři se nachází nastavení a připojení k email serveru a error handler
- Třida `model/API.php` zajišťuje práci s API rozhrním aplikace
### Použité frameworky
- Pro UI používám [UIkit](https://getuikit.com/docs/introduction)
- Ke generování tabulek používám [DataTables](https://datatables.net/)
- Pro generování krásných grafů používám [ChartsJS](https://www.chartjs.org/)
- Pro posílání emailů používám [PHPMailer](https://github.com/PHPMailer/PHPMailer)

