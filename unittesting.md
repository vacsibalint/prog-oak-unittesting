Tegyük fel, hogy szeretnénk egy Log nevű PHP osztályt implementálni, ami egyszerű naplózási műveleteket valósít meg. Mielőtt elkezdenénk a Log osztály kódolását, először meg kellene határozni, hogy mit csináljon ez az osztály. (Layering)

 Jobb fejlesztők első lépése az UML diagram megrajzolása, ami alapján később a teszteket és a végleges kódot is írják. A Log osztály legyen képes naplófájlokat létrehozni és ha a naplófájl már létezik, csak nyissa azt meg példányosításkor, ne ürítse ki a tartalmát. A Log osztály legyen képes a példányosításkor megnyitott fájlba naplóüzenetet írni. Számos elvárást fogalmazhatunk még meg, de a példa kedvéért csak ezt a kettőt fogom bemutatni. A project manager már megrajzolta az UML diagramokat, a fejlesztők pedig tudnak róla, hogy a cég minőségbiztosítása megköveteli a tesztek írását.

Két tesztet is szeretnénk elvégezni, ezért az alábbi funkciókat is hozzáadhatjuk a PHPUnit osztályhoz:
--
class LogTest extends PHPUnit_TestCase
{
    private $log;
    public function setUp(){ }
    public function tearDown(){ }
    public function testLogFileExists(){ }
    public function testDontPurgeLogFile(){ }
}

A setUp() függvény a teszt indításakor fog lefutni és ha elindult, a PHPUnit egyszerűen az összes test-el kezdődő függvényt végrehajtja, majd lezárásként a tearDown() következik, ahol jellemzően a megnyitott fájlok, megnyitott kapcsolatok zárhatóak le.

A setUp()-ban létrehozzuk a Log osztály egyik példányát. Fontos, hogy a __constructor egyetlen paramétere már tartalmazza a naplófájl nevét is:

--
public function setUp(){
    $this->log = new Log('/tmp/unittest.log');
}
--

A tearDown()-ban a létrehozott osztály példányát illik törölni. Ha megtesszük, a garbage collectornak is kevesebb dolga lesz. (ugye tudjuk mi az a garbage collector?!)
Ezen kívül itt törölni is kell a létrehozott logfájlt, a fájlnevet a Log osztály eltárolja, ezért az onnan nyerjük ki, hogy ne kelljen feleslegesen mégyegyszer begépelni itt is a fájlnevet:

--
public function tearDown(){
    unlink($this->log->filename);
    unset($this->log);
}
--

Mivel a testLogFileExists() a setUp() után fog lefutni, itt kell ellenőrizni, hogy a fájl valóban létrejött-e. A PHPUnit szintaktikája nagyon egyszerű, mindössze ennyit kell tennünk:
--
public function testLogFileExists(){
    $this->assertTrue(file_exists($this->log->filename));
}
--
Ha a fájl ezen a ponton nem létezik, a teszt lefuttatásakor hibát fogunk kapni, mivel a teszt egyik állítása (assert) nem lett igaz. A következő teszt annak a vizsgálata, hogy a fájlba írás után és újra megnyitva a fájlt ezzel az osztállyal nem egy üres fájlt kapunk. Bár én nem bíznám rá a banki tranzakciók logját, de ez a teszt arra is alkalmas, hogy megnézze, valóban beleírt-e a fájlba a Log osztály. A fájlnevet megint a példányból olvassuk ki:

--
public function testDontPurgeLogFile(){
    $this->log->write("Hello world!");
    $filename = $this->log->filename;
    unset($this->log);
    $this->log = new Log($filename);
    $this->assertTrue(filesize($filename) > 0);
}
--
Ennyi. 
Miért jó ez? Nem szükséges éles környezetbe tenni az osztályt ahhoz, hogy megtudjuk, helyesen működik-e. Ha később új képességekkel szeretnénk felruházni, kibővíthetjük a LogTest osztályt a megfelelő funkciókkal, majd anélkül tesztelhetjük a helyes működést, hogy a teljes alkalmazást el kelljen indítani és egyúttal lefutnak a korábbi tesztek is, így biztosak lehetünk abban, hogy az új feladat megvalósításával nem rontottunk el olyat, ami korábban már működött. 

A legfontosabb, hogy a felhasználó által végzett teszt sokkal lassabb, mint az automatizált tesztek és nem is biztos, hogy a fejlesztő észreveszi, hogy hol hibázott.