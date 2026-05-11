import os, glob

html_files = glob.glob('*.html')

for filepath in html_files:
    with open(filepath, 'r', encoding='utf-8') as f:
        content = f.read()

    # 1. Update navbar
    nav_search = '<a href="./donar.html" class="btn btn--red">COLABORÁ</a>'
    nav_replace = '<a href="https://docs.google.com/forms/d/e/1FAIpQLSc4DsRGqnbUVHweHnIdXBZ9ygT_4WXOQtL_OMLgvkPzBGbZQQ/viewform" target="_blank" rel="noopener noreferrer" class="btn btn--gold">ASOCIATE</a>\n            <a href="./donar.html" class="btn btn--red">COLABORÁ</a>'
    
    if nav_search in content and '<a href="https://docs.google.com/forms' not in content:
        content = content.replace(nav_search, nav_replace)

    # 2. Update footer
    footer_search = '''                    <li><a href="./contacto.html">Necesito asistencia</a></li>
                </ul>
            </div>'''
    footer_replace = '''                    <li><a href="./contacto.html">Necesito asistencia</a></li>
                </ul>
                <a href="https://docs.google.com/forms/d/e/1FAIpQLSc4DsRGqnbUVHweHnIdXBZ9ygT_4WXOQtL_OMLgvkPzBGbZQQ/viewform" target="_blank" rel="noopener noreferrer" class="btn btn--gold" style="margin-top: 16px; display: inline-flex;">ASOCIATE</a>
            </div>'''
            
    if footer_search in content:
        content = content.replace(footer_search, footer_replace)
    
    with open(filepath, 'w', encoding='utf-8') as f:
        f.write(content)
    
    print(f"Updated {filepath}")
