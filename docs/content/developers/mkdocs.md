# MkDocs

## Installation

1. install python3
2. install mkdocs + plugins via `pip3` (__do not use unmaintained "apt" packages!__)
3. ```
    pip3 install mkdocs
    pip3 install mkdocs-material
    pip3 install mkdocs-minify-plugin
    pip3 install mkdocs-redirects
    ```

## Commands

* `mkdocs new [dir-name]` - Create a new project.
* `mkdocs serve` - Start the live-reloading docs server.
* `mkdocs build` - Build the documentation site.
* `mkdocs help` - Print this help message.

## Project layout

      mkdocs.yml                # The configuration file.
      docs/
         content/
            index.md             # The documentation homepage.
               ...               # Other markdown pages, images and other files.
            developers/
            users/
         overrides/              # Theme overrides
