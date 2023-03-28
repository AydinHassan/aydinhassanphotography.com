{ pkgs, lib, config, ... }:

{
  languages.javascript = {
    enable = lib.mkDefault true;
    package = lib.mkDefault pkgs.nodejs-18_x;
  };

   processes = {
      vite.exec = "npm install && npm run dev";
    };
}

