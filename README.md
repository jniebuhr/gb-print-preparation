Goldbek Print Preparation
=========================

Dieses Tool bietet einen Kommandozeilenbefehl an mit dem ein Druckbogen für eine bestimmte Karte generiert werden kann.

## Verwendung

Es wird eine JSON Datei generiert und als Config Parameter angegeben. (Siehe `doc/examples/job.json`)

    ./bin/prepare generate -c job.json
    
## Libraries

Für die Realisierung der CLI App wird das [CLIFramework](https://github.com/c9s/CLIFramework) benutzt.
Für die Generierung der PDF Dateien wird [ZendPdf](https://github.com/zendframework/ZendPdf) verwendet.

## Konfiguration

Die Konfiguration wird in der Datei `config/main.php` verwaltet. Hier sind die möglichen Formate und Größen definiert.