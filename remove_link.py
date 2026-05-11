import os, glob, re

html_files = glob.glob('*.html')

for filepath in html_files:
    with open(filepath, 'r', encoding='utf-8') as f:
        content = f.read()

    # We want to remove the specific line from the footer:
    # <li><a href="./asociarse.html">Asociarse</a></li>
    # It might have leading spaces.
    
    new_content = re.sub(r'^[ \t]*<li><a href="\./asociarse\.html">Asociarse</a></li>\r?\n', '', content, flags=re.MULTILINE)
    
    if new_content != content:
        with open(filepath, 'w', encoding='utf-8') as f:
            f.write(new_content)
        print(f"Updated {filepath}")
