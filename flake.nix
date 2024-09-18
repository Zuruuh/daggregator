{
  inputs = {
    nixpkgs.url = "github:nixos/nixpkgs?ref=24.05";
    unstable-pkgs.url = "github:nixos/nixpkgs?ref=770e9947f466ce80c414702b0db27123dd985eb8";
    flake-utils.url = "github:numtide/flake-utils";
  };

  outputs = { self, unstable-pkgs, nixpkgs, flake-utils }:
    flake-utils.lib.eachDefaultSystem (system:
      let
        pkgs = nixpkgs.legacyPackages.${system};
        unstable = unstable-pkgs.legacyPackages.${system};

        php = unstable.php83.buildEnv {
          extensions = ({ enabled, all }: enabled ++ (with all; [
            redis
            pcov
          ]));
        };

        packages = with pkgs; [
          unstable.docker_27
          python312
          unstable.uv
          unstable.bun
          unstable.biome
          symfony-cli
          pulumi
          gnupg
          sops
          php
          php.packages.composer
          just
        ];
      in
      {
        devShell = pkgs.mkShell {
          buildInputs = packages;
        };
      }
    );
}
