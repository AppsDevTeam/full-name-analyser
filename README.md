# Full Name Analyser

- parsuje zadaný řetězec na křestní jméno, příjmení a titul před jménem a za jménem, přičemž se nenechá rozhodit více křestními jmény / příjmeními / tituly najednou, přehozeným křestním jménem a příjmením nebo zadáním jména bez diakritiky
- snaží se detekovat pohlaví, přičemž kromě databáze křestních jmen (obsahující cca 25 000 křestních jmen z webu https://www.behindthename.com/, poslední aktualizace 14. 11. 2022) bere v potaz i příjmení a unisex křestní jména (například Alex, Nikola a podobně)
- generuje vokativ, přičemž bere v potaz zadané tituly (pro "MUDr. Jan Novák" vygeneruje "doktore")
- zaměřuje se především na správnou funkčnost pro česká a slovenská jména
- snaží se detekovat vstup, který není jménem osoby
- nesnaží se o zbytečnou magii (převést řetězec bez diakritiky na řetězec s diakritikou za pomocí četnosti jména v Česku/Slovensku a podobně)
- 100% kódu pokrytého automatickými testy 

## Použití

```php
$result = (new FullNameAnalyser('cs', 'CZ', \ADT\FullNameAnalyser\FullNameAnalyser::VOCATIVE_FORM_INFORMAL))->anylyse('prof. Ing. Tomáš Kudělka, Ph.D.');

$result->getTitleBefore(); // vrátí "prof. Ing."
$result->getFirstName(); // vrátí "Tomáš"
$result->getLastName(); // vrátí "Kudělka"
$result->getTitleAfter(); // vrátí "Ph.D."
$result->getGender(); // vrátí "male" (alternativně "female")
$result->getVocative(); // vrátí "Tomáši" (alternativně "profesore")
```

Povolené jazyky: `cs`, `sk`

Povolené země: `CZ`, `SK`

Povolené formy oslovení: `VOCATIVE_FORM_INFORMAL`, `VOCATIVE_FORM_FORMAL`

V případě, že znáte pohlaví, můžete jej předat jako druhý parametr do metody `analyse`.

## Poděkování

- https://www.behindthename.com/
- https://github.com/Anwarvic/Behind-The-Name
- https://github.com/granam/czech-vocative
