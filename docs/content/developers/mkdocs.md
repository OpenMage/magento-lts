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

!!! danger

      Do not use unmaintained `apt` packages.

## Commands

Create a new project

```bash 
mkdocs new [dir-name]
```

Start the live-reloading docs server

```bash 
mkdocs serve
```

Build the documentation site

```bash 
mkdocs build
```

Print this help message

```bash 
mkdocs help
```

## Project layout

```
mkdocs.yml                 # The configuration file.
docs/
   content/
      index.md             # The documentation homepage.
         ...               # Other markdown pages, images and other files.
      blog/                # Blog-like entries (with date, author, ...)
         assets/
         posts/
      developers/
      users/
   overrides/              # Theme overrides
docs_includes/             # Need to stay outside "docs"
```

[^1]: see [How to Install Python on Your System: A Guide](https://realpython.com/installing-python/)
