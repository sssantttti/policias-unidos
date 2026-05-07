import os, re
import glob

html_files = glob.glob('*.html')

for filepath in html_files:
    with open(filepath, 'r', encoding='utf-8') as f:
        content = f.read()

    # Regex to find the <nav class="navbar__links" id="primary-nav"> block
    pattern = re.compile(r'(<nav class="navbar__links" id="primary-nav">)(.*?)(</nav>)', re.DOTALL)
    
    def replacer(match):
        inner_nav = match.group(2)
        
        # Check if nosotros or que-hacemos were active
        nosotros_match = re.search(r'<a href="\./nosotros\.html"[^>]*>', inner_nav)
        que_hacemos_match = re.search(r'<a href="\./que-hacemos\.html"[^>]*>', inner_nav)
        
        nosotros_active = 'class="active"' in nosotros_match.group(0) if nosotros_match else False
        que_hacemos_active = 'class="active"' in que_hacemos_match.group(0) if que_hacemos_match else False
        
        dropdown_active_class = ' active' if (nosotros_active or que_hacemos_active) else ''
        nosotros_cls = ' class="active"' if nosotros_active else ''
        que_hacemos_cls = ' class="active"' if que_hacemos_active else ''
        
        new_nav = f'''
            <div class="nav-dropdown">
                <span class="nav-dropdown-toggle{dropdown_active_class}">Nosotros</span>
                <div class="nav-dropdown-menu">
                    <a href="./nosotros.html"{nosotros_cls}>Quiénes Somos</a>
                    <a href="./que-hacemos.html"{que_hacemos_cls}>Qué Hacemos</a>
                </div>
            </div>'''
            
        programas_match = re.search(r'<a href="\./programas\.html"[^>]*>.*?</a>', inner_nav)
        if programas_match:
            new_nav += '\n            ' + programas_match.group(0)
            
        empresas_match = re.search(r'<a href="\./empresas\.html"[^>]*>.*?</a>', inner_nav)
        if empresas_match:
            new_nav += '\n            ' + empresas_match.group(0)
            
        noticias_match = re.search(r'<a href="\./noticias\.html"[^>]*>.*?</a>', inner_nav)
        if noticias_match:
            new_nav += '\n            ' + noticias_match.group(0)
            
        contacto_match = re.search(r'<a href="\./contacto\.html"[^>]*>.*?</a>', inner_nav)
        if contacto_match:
            new_nav += '\n            ' + contacto_match.group(0)
            
        new_nav += '\n        '
        
        return match.group(1) + new_nav + match.group(3)

    new_content = pattern.sub(replacer, content)
    
    if new_content != content:
        with open(filepath, 'w', encoding='utf-8') as f:
            f.write(new_content)
        print(f'Updated {filepath}')
