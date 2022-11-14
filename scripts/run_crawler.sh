#!/usr/bin/env bash

cd scripts/crawler && docker run -it --rm -v "$PWD":/usr/src/myapp -w /usr/src/myapp python:2 bash -c "pip install requests && pip install bs4 && pip install pandas && pip install lxml && python 01-extract_info.py && python 02-combine_into_one_pickle.py && python 03-prune.py"