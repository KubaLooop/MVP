# Analýza DB:
Bude potřeba mít model uživatele a absence.
Uživatel musí mít rozlišení rolí, aby vedoucí mohl schvalovat neschválené volna zaměstnanců.
Absence bude obsahovat typ, bude patřit (BelongTo) uživateli, který ji vytvořil. Dále musí obsahovat typ absence a začátek + konec a hodiny. Hodiny použiju u jednodenních absencí pro zadání počtu hodin. Pokud se jedná o více dní, tak při zadávání datumů se vypočítá počet hodin celkem. (Přepřipraveno pro export). Dále bych mohl ukládat například atribut který vedoucí schválil volno, ale nepřijde mi to podstané.

![ER diagram](ER.jpg)

V ER diagramu jsou popsány Enum tabulky, které samozřejmě nebudou řešeny na úrovni databáze, ale pro přehlednost jsem je zde zakreslil.

# Další postup:
- Pro urychlení práce jsem se rozhodl použít Laravel, abych se většinu času mohl věnovat napojení na API a logice místo napojování MySQL a routování a řešení mailů. Jako druhý důvod zvolení Laravelu je, že je to jediný framework, se kterým mám zkušenosti.
- Views budu dělat jako poslední, pokud mi zbyde čas a budou z většiny generované (views beru jako nejméně prioritní v tomto zadání - nejsou ani zmíněné)
- Po analýze budu pracovat na vytvoření modelů. Pozn.: Pro uživatele jsem nedělal ruční model a migraci a použil tu výchozí.