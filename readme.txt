=== Corona-Test-Verify ===
Contributors: Silvio Osowsky
Donate link: https://osowsky-webdesign.de
Tags: corona, test, verify, employee, 
Requires at least: 5.0
Tested up to: 5.8.2
Stable tag: 1.5.0
Requires PHP: 7.0
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

## Beschreibung
Nach §28 gilt seit dem 24. November für alle Betriebe der so genannte 3G-Status. D. h. Mitarbeiter dürfen ihren Betrieb nur betreten, wenn diese über einen gültigen 3G-Status (geimpft, genesen oder getestet) verfügen. Auch wenn Mitarbeiter den jeweils Verantwortlichen gegenüber einen der drei Nachweise vorlegen müssen, sind Arbeitgeber gut beraten, gültige Datenschutzbestimmungen im Blick zu haben. Denn auch diese Daten unterliegen dem Datenschutz und dürfen nur insoweit erhoben und verarbeitet werden, wie dies auf Grund dieser Gesetzeslage notwendig ist. 

Genau hier setzt dieses PlugIn an. Denn es erlaubt den Arbeitgeber, Verantwortliche zu benennen, die den jeweils vorgelegten Nachweis prüfen und dem Mitarbeiter danach eine einheitliche Bescheinigung auszustellen, aus der für andere nicht mehr erkennbar ist, welcher der drei Nachweise zu einem gültigen 3G-Status für diesen Mitarbeiter geführt hat. Die Prüfergebnisse werden für jeden Mitarbeiter hier erfasst. Danach müssen Mitarbeiter ihren Status innerhalb der Gültigkeitsdauer nicht erneut belegen, sondern haben vielmehr die Möglichkeit ihren jeweiligen Status per Smartphone abzurufen.

Wenn es im Unternehmen einen zertifizierten Tester gibt, kann dieser einen geeigneten Test durchführen und diesen papierlos im System hinterlegen.
Gleichzeitig erfüllen Arbeitnehmer mit den hier erzeugten Datensätzen Ihre Dokumentationspflicht und sind so in der Lage diese zu belegen. 

Darüber hinaus erlaubt es den Mitarbeitern mit diesem Abruf auch Dritten gegenüber (z. B. andere Mitarbeiter, Kunden oder Lieferanten) ihren gültigen Status nachzuweisen. Diese Nachweis ist mit einem verschlüsselten QR-Code ausgestattet, über den Dritte dann wiederum die aktuelle Gültigkeit diese Vorlage prüfen können. Der vom QR-Code erzeugte Link ist dabei zeitlich befristet. 

Voraussetzung für die Nutzung dieses PlugIns ist, dass die Mitarbeiter als User im Dashboard angelegt sind.

## Funktionsweis
* Das Plugin „corona-verify“ wird im Wordpress System installiert
* Es werden im Dashboard die Menüpunkte „Corona-Admin“ und die Unterpunkte „Mitarbeiter“ und „Testübersicht“ angezeigt
* Menüpunkt „Corona-Admin“ 
    * Erklärt die Funktionsweise des shortcode
    * Stellt Einstellungen und Optionen zur Verfügung
* Menüpunkt „Mitarbeiter“
    * Mitarbeiter hinzufügen (vorher muß der Mitarbeiter im Wordpress System angelegt wurden sein)
    * Mitarbeiter Überblick
* Menüpunkt „Testverwaltung“
    * Eintragen von Tests zu einem Mitarbeiter (Test wird standardgemäß auf 24 Gültigkeit gesetzt.)
    * Übersicht durchgeführter Tests pro Mitarbeiter
* Einbinden eines vom Plugin bereitgestelltem ShortCode `[corona-verify-form]`
* Auf der Seite, auf der der ShortCode eingebunden wird, wird vom angemeldeten Mitarbeiter der aktuelle Teststatus angezeigt. 
    * Status kann sein
        * Test Negativ und aktuell
        * Test Positiv und aktuell 
        * Kein gültiger Test vorhanden. (Der Test ist immer 24 Stunden gültig)
* Bei einem gültigen negativen Test und der Einstellung der ShortCode Eigenschaft „QR“ auf den Wert „1“ wird ein QR Code für den Kunden angezeigt. 
* Der QR Code leitet auf eine Seite mit dem jeweiligen aktuellen  Testergebnis. 
* Die Infos dieser Seite werden nur solange angezeigt, wie der Test Gültigkeit besitzt. 

---
## Description
According to §28, the so-called 3G status has been in effect for all businesses since November 24th. I. E. Employees are only allowed to enter their company if they have a valid 3G status (vaccinated, recovered or tested). Even if employees have to present one of the three proofs to the responsible person, employers are well advised to keep an eye on valid data protection regulations. Because this data is also subject to data protection and may only be collected and processed to the extent that this is necessary on the basis of this legal situation.
This is exactly where this plug-in comes in. This is because it allows the employer to name responsible persons who check the evidence submitted and then issue the employee with a uniform certificate from which it is no longer possible for others to recognize which of the three evidence has led to a valid 3G status for this employee. The test results are recorded here for each employee. After that, employees do not have to prove their status again within the period of validity, but rather have the option of calling up their respective status via smartphone.
If there is a certified tester in the company, he can carry out a suitable test and store it in the system without paper. At the same time, employees fulfill their documentation requirements with the data records generated here and are thus able to prove them.
In addition, this request allows employees to prove their valid status to third parties (e.g. other employees, customers or suppliers). This proof is equipped with an encrypted QR code, which third parties can then use to check the current validity of this template. The link generated by the QR code is limited in time.
The prerequisite for using this plug-in is that the employees are created as users in the dashboard.

## How it works
* The "corona-verify" plugin is installed in the Wordpress system
* The menu items "Corona-Admin" and the sub-items "Employees" and "Test overview" are displayed in the dashboard
* "Corona-Admin" menu item
    * Explains how the shortcode works
    * Provides settings and options
* Menu item "Employees"
    * Add employees (the employee must have been created in the Wordpress system beforehand)
    * Employee overview
* "Test management" menu item
    * Entering tests for an employee (test is set to validity 24 by default.)
    * Overview of tests carried out per employee
* Integration of a short code provided by the plug-in "[corona-verify-form]"
* On the page on which the ShortCode is integrated, the current test status is displayed by the logged-in employee.
    * Status can be
        * Test negative and current
        * Test positive and current
        * No valid test available. (The test is always valid for 24 hours)
* With a valid negative test and the setting of the ShortCode property "QR" to the value "1", a QR code is displayed for the customer.
* The QR code leads to a page with the current test result.
* The information on this page is only displayed as long as the test is valid.