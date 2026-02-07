# Analýza DB:
Bude potřeba mít model uživatele a absence.
Uživatel musí mít rozlišení rolí, aby vedoucí mohl schvalovat neschválené volna zaměstnanců.
Absence bude obsahovat typ, bude patřit (BelongTo) uživateli, který ji vytvořil. Dále musí obsahovat typ absence a začátek + konec a hodiny. Hodiny použiju u jednodenních absencí pro zadání počtu hodin. Pokud se jedná o více dní, tak při zadávání datumů se vypočítá počet hodin celkem. (Přepřipraveno pro export). V návrhu databáze nepočítám s možností, že systém pracuje i s časem - nepůjde zadat přesný čas odchodu, ale pouze rozsah dní nebo jednodenní volno a počet hodin, co bude zaměstnanec pryč. Dále bych mohl ukládat například atribut který vedoucí schválil volno nebo záznam důvodu nepřijetí,.. ale nepřijde mi to pro prototyp podstatné.

![ER diagram](ER.jpg)

V ER diagramu jsou popsány Enum tabulky, které samozřejmě nebudou řešeny na úrovni databáze, ale pro přehlednost jsem je zde zakreslil. Zrušení žádosti jsem si interpretoval jako smazání záznamu a informování uživatele, že byla žádost zrušena.

# Další postup:
## A
- Pro urychlení práce jsem se rozhodl použít Laravel, abych se většinu času mohl věnovat napojení na API a logice místo napojování MySQL a routování a řešení mailů. Jako druhý důvod zvolení Laravelu je, že je to jediný framework, se kterým mám zkušenosti.
- Views budu dělat jako poslední, pokud mi zbyde čas a budou z většiny generované (views beru jako nejméně prioritní v tomto zadání - nejsou ani zmíněné)
- Po analýze budu pracovat na vytvoření modelů. Pozn.: Pro uživatele jsem nedělal ruční model a migraci a použil tu výchozí.

## B
- Dále bude potřeba vytvořit logiku žádostí. Nebyl jsem si jistý, zda je čistá logika žádostí (počítání hodin a ignorování víkendů,..) se hodí implementovat v Controlleru (moje jediná zkušenost). Pomocí LLM jsem se dozvěděl, že mám pracovat se Services. Hlavní důvodem jsem pochopil, že je lepší modularita a testovatelnost. Controller bych měl použít pouze pro validaci/auth/response logiku.
- U jednodenní žádosti stačí zkontrolovat, zda jsou zadané hodiny, co chce být zaměstnanec pryč nebo je nastavit na 8h (celý den).
- U vícedenního volna zjistím počet dní, odstraním víkendy a pronásobím hodinami (8h/den) a uložím do atributu "hours".
- Po vytvoření logiky musím vytvořit Event pro tvorbu emailů při změně stavu žádostí.
- Jakmile budu hotový, tak je potřeba vytvořit controller pro žádosti, který bude validovat vstupy a poté otestovat.
- artisan tinker test: Funguje vytvoření uživatelů a žádostí a schválení. Pokud se změní stav, tak v logu správně vidím příchozí mail.
- TODO: před dalším commitem: Dodělat controller a otestovat API