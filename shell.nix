{ pkgs ? import <nixpkgs> { } }:

let
  projectInstall = pkgs.writeShellApplication {
    name = "project-install";
    runtimeInputs = [
      pkgs.php81
      pkgs.php81Packages.composer
    ];
    text = ''
      composer install --prefer-dist --no-progress --working-dir="$PROJECT_ROOT"
    '';
  };
  projectValidateComposer = pkgs.writeShellApplication {
    name = "project-validate-composer";
    runtimeInputs = [
      pkgs.php81
      pkgs.php81Packages.composer
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
      xmllint --schema xliff-core-1.2-strict.xsd --noout "$(find Resources -name '*.xlf')"
    '';
  };
  projectCodingGuideline = pkgs.writeShellApplication {
    name = "project-coding-guideline";
    runtimeInputs = [
      pkgs.php81
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
      pkgs.php81
    ];
    text = ''
      project-install

      _instance_path="$PROJECT_ROOT/.Build/web/typo3temp/var/tests/acceptance/"

      mkdir -p "$_instance_path"

      geckodriver > /dev/null 2>&1 &
      _pid_geckodriver=$!

      TYPO3_PATH_APP="$_instance_path" \
        TYPO3_PATH_ROOT="$_instance_path" \
        php -S 127.0.0.1:8080 -t "$_instance_path" > /dev/null 2>&1 &
      _pid_php=$!

      ./vendor/bin/codecept build
      ./vendor/bin/codecept run

      kill "$_pid_geckodriver" "$_pid_php"
    '';
  };

in pkgs.mkShell {
  name = "TYPO3 Extension Watchlist";
  buildInputs = [
    projectValidateComposer
    projectValidateXml
    projectCodingGuideline
    projectTestAcceptance
  ];

  shellHook = ''
    export PROJECT_ROOT="$(pwd)"

    export typo3DatabaseDriver=pdo_sqlite
  '';
}
