{ pkgs ? import <nixpkgs> { } }:

let
  php = pkgs.php82;
  inherit(pkgs.php81Packages) composer;

  projectInstall = pkgs.writeShellApplication {
    name = "project-install";
    runtimeInputs = [
      php
      composer
    ];
    text = ''
      rm -rf .Build/ vendor/
      composer install --prefer-dist --no-progress --working-dir="$PROJECT_ROOT"
    '';
  };
  projectValidateComposer = pkgs.writeShellApplication {
    name = "project-validate-composer";
    runtimeInputs = [
      php
      composer
    ];
    text = ''
      composer validate
    '';
  };
  projectValidateXml = pkgs.writeShellApplication {
    name = "project-validate-xml";
    runtimeInputs = [
      pkgs.libxml2
      pkgs.wget
      projectInstall
    ];
    text = ''
      project-install
      xmllint --schema vendor/phpunit/phpunit/phpunit.xsd --noout phpunit.xml.dist
      wget --no-check-certificate https://docs.oasis-open.org/xliff/v1.2/os/xliff-core-1.2-strict.xsd --output-document=xliff-core-1.2-strict.xsd
      # shellcheck disable=SC2046
      xmllint --schema xliff-core-1.2-strict.xsd --noout $(find Resources -name '*.xlf')
    '';
  };
  projectCodingGuideline = pkgs.writeShellApplication {
    name = "project-coding-guideline";
    runtimeInputs = [
      php
      projectInstall
    ];
    text = ''
      project-install
      ./vendor/bin/php-cs-fixer fix --dry-run --diff
    '';
  };
  projectTestAcceptance = pkgs.writeShellApplication {
    name = "project-test-acceptance";
    runtimeInputs = [
      projectInstall
      pkgs.sqlite
      pkgs.firefox
      pkgs.geckodriver
      php
    ];
    text = ''
      project-install

      export INSTANCE_PATH="$PROJECT_ROOT/.Build/web/typo3temp/var/tests/acceptance"

      mkdir -p "$INSTANCE_PATH"
      ./vendor/bin/codecept run
    '';
  };

in pkgs.mkShell {
  name = "TYPO3 Extension Watchlist";
  buildInputs = [
    projectValidateComposer
    projectValidateXml
    projectCodingGuideline
    projectTestAcceptance
    php
    composer
  ];

  shellHook = ''
    export PROJECT_ROOT="$(pwd)"

    export typo3DatabaseDriver=pdo_sqlite
  '';
}
