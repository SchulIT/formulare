# Formularschrank

Mithilfe dieses Projektes lassen sich (auch komplexe) Formulare erstellen.

## Voraussetzungen

* PHP 7.4
* MariaDB 10.4+

Da das Tool Formulardaten als JSON in der Datenbank ablegt und die JSON-Funktionen von MariaDB verwendet (welche sich leider zu MySQL unterscheiden),
muss zwingend MariaDB verwendet werden.

## Installation

```bash
$ git clone https://github.com/SchulIT/formulare.git
$ composer install --no-dev --optimize-autoloader --no-scripts
$ php bin/console app:create-certificate --type saml
```

Nun die Informationen des Identity Providers unter `saml/idp.xml` ablegen.

```bash
$ php bin/console cache:clear
$ php bin/console doctrine:migrations:migrate --no-interaction
```

Nun die Formulardateien in `forms/` hinterlegen und die entsprechenden Rollen verteilen (siehe unten).

## Funktionsweise

Für ein Formular werden im Wesentlichen drei Dinge benötigt:

1. eine YAML-Konfigurationsdatei im Ordner `forms/`
2. eine Formularklasse zur Anzeige und Konfiguration des Formulars, ebenfalls im Ordner `forms/`

### Konfigurationsdatei

Die Konfigurationsdatei hat folgenden Aufbau:

```yaml
form:
  alias:                      # Dies ist der eindeutige Alias des Formulars
    name: Name des Formulars
    introduction: Ein Einführungstext, der oberhalb des Formulars angezeigt wird
    success: Eine Erfolgsnachricht, die angezeigt wird, wenn das Formular abgeschickt wurde.
    role: ROLE_ALIAS          # Die Rolle, die der administrative Benutzer innehaben muss, um das Formular verwalten zu können
      
    form_class: App\Form\FormType          # Die Formularklasse (siehe Punkt 3 oben)
    
    # In items werden Informationen zu den einzelnen Formularfelder gespeichert. Der Schlüssel muss dabei stehts
    # mit dem zugehörigen Feld in der Datenklasse (siehe data_class) übereinstimmen.
    # Als einziges Feld ist `label` jeweils erforderlich. Alle anderen Labels können bei Bedarf anders benannt werden.
    items:
      firstname:
        label: Vorname
      email:
        label: E-Mail-Adresse
        help: Dieser Text kann bspw. als Hilfetext angezeigt werden.
      auswahl:
        label: Ihre Auswahl
        choices:
          one: Erste Wahl
          two: Zweite Wahl
        seats:                # Optional (seats wird an vielen Stellen vom System automatisch erkannt)
          one: 10             # Für die erste Wahl gibt es maximal 10 Plätze
          two: 5
      zustimmung:
        label: Ich stimme zu
        checkbox: ~           # So wird sichergestellt, dass die Werte 0 und 1 als X oder als Häckchen dargestellt werden

    # Optional, falls eine Anmeldung erforderlich ist, um das Formular abzuschicken. Wenn keine Anmeldung erforderlich ist,
    # kann der Abschnitt weggelassen werden.
    # Der gesamte Abschnitt wird in die Sicherheitskonfiguration eingefügt (so als würde man sie in security.yaml notieren).
    # Dokumentation siehe Symfony Docs.
    security:
      providers:
        # Eigener 
        grundschueler_users:
          memory:
            users:
              # Schlüssel ist der Benutzername (der jedoch nicht eingegeben werden muss)
              # Das Passwort mittels `php bin/console security:hash-password` generieren
              # Die Rolle darf nicht mit der in oberen Teil als `role` definierten Rolle übereinstimmen!!
              grundschueler: { password: '$2y$13$I8YaJJMTvZLIQW/RgJjOuuCtWdENYSOKU2e0MpvoddxkrAwgKxWyy', roles: ROLE_GRUNDSCHUELER_USER }
    
      firewalls:
        # Login und Ende sind Seiten ohne Benutzerkennung
        grundschueler_login:
          pattern: ^/grundschueler/(login|success)$
          anonymous: ~
    
        grundschueler:
          pattern: ^/grundschueler
          guard:
            provider: grundschueler_users
            authenticators:
              - App\Security\Frontend\LoginFormAuthenticator
          logout:
            path: form_logout
            success_handler: App\Security\Frontend\LogoutHandler
    
      access_control:
        - { path: ^/grundschueler/(login|success), roles: IS_AUTHENTICATED_ANONYMOUSLY  }
        - { path: ^/grundschueler, roles: ROLE_GRUNDSCHUELER_USER }
```

### Formular-Klasse

Die Klasse muss von der Klasse `AbstractFormType` erben. Diese wiederum erbt von der Klasse
`AbstractType`. Im Array `$options['items']` stehen alle Werte aus dem Knoten `items` (siehe Konfigurationsdatei)
zur Verfügung.

```php
<?php

namespace App\Form;

use Symfony\Component\Form\Extension\Core\Type\CheckboxType;use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;use Symfony\Component\Validator\Constraints\Choice;use Symfony\Component\Validator\Constraints\NotBlank;use Symfony\Component\Validator\Constraints\NotNull;

class FormType extends AbstractFormType {
    public function buildForm(FormBuilderInterface $builder, array $options) {
         $builder
            ->add('firstname', TextType::class, [
                'label' => $options['items']['firstname']['label'],
                'constraints' => [
                    new NotBlank()
                ]
            ])
            ->add('email', EmailType::class, [
                'label' => $options['items']['email']['label'],
                'help' => $options['items']['email']['help']
            ])
            ->add('auswahl', ChoiceType::class, [
                'label' => $options['items']['auswahl']['label'],
                'choices' => array_flip($options['items']['auswahl']['choices']),
                'placeholder' => 'Bitte auswählen',
                'constraints' => [
                    new NotNull(),
                    new Choice(array_keys($options['items']['auswahl']['choice']))
                ]
            ])
            ->add('zustimmung', CheckboxType::class, [
                'label' => $options['items']['zustimmung']['label']
            ]);
    }
}
```

#### Verfügbare Sitzplätze

Möchte man eine Auswahl auf eine bestimmte Anzahl von (bspw.) Sitzplätzen beschränken, so steht dafür das Formularelement
`SeatType` zur Verfügung:

```php
$builder->add('event', SeatsType::class, [
    'label' => $options['items']['auswahl']['label'],
    'choices' => array_flip($options['items']['auswahl']['choices']),
    'form' => 'grundschueler'       # Der Alias des Formulars
]);
```

Entsprechend muss man die Validierung ergänzen:

```php
$builder->add('event', SeatsType::class, [
    'label' => $options['items']['event']['label'],
    'choices' => array_flip($options['items']['event']['choices']),
    'seats' => $options['items']['event']['seats'],
    'form' => 'infoabend',
    'constraints' => [
        new NotNull(),
        new Choice(array_keys($options['items']['event']['choices'])),
        new Seats(['form' => 'infoabend'])      # Wichtig: Hier muss wieder der Formularalias übergeben werden
    ]
]);
```

