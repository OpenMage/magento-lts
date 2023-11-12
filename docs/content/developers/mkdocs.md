---
tags:
- Documentation
---

# MkDocs

`mkdocs` is used to build this documentation.

## Installation

1. Install `python3`[^1]

    ```bash
    sudo apt-get update
    sudo apt-get install python3.8 python3-pip
    ```

2. Install `mkdocs` and plugins via `pip3`[^2]

    ```bash
    pip3 install mkdocs mkdocs-material mkdocs-minify-plugin mkdocs-redirects
    ```

## Commands

* `mkdocs new [dir-name]` - Create a new project.
* `mkdocs serve` - Start the live-reloading docs server.
* `mkdocs build` - Build the documentation site.
* `mkdocs help` - Print this help message.

## Project layout

      mkdocs.yml                 # The configuration file.
      docs/
         content/
            index.md             # The documentation homepage.
               ...               # Other markdown pages, images and other files.
            developers/
            users/
         overrides/              # Theme overrides

[^1]: see https://realpython.com/installing-python/
[^2]: do not use unmaintained `apt` packages