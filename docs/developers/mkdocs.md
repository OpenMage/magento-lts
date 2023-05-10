# Documentation

## Installation

1. install python3
2. install mkdocs + plugins via `pip3` (__do not use unmaintained "apt" packages!__)
    ```
    pip3 install mkdocs
    pip3 install mkdocs-material
    pip3 install mkdocs-redirects
    pip3 install mkdocs-minify-plugin
    ```

## Commands

* `mkdocs new [dir-name]` - Create a new project.
* `mkdocs serve` - Start the live-reloading docs server.
* `mkdocs build` - Build the documentation site.
* `mkdocs help` - Print this help message.

## Project layout

    mkdocs.yml    # The configuration file.
    docs/
        index.md  # The documentation homepage.
        ...       # Other markdown pages, images and other files.
