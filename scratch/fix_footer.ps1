$files = Get-ChildItem *.html
foreach ($f in $files) {
    $content = Get-Content $f.FullName -Raw
    $content = $content -replace 'CUIT 30-71934580-4 · Personería Jurídica IGJ', 'CUIT 30-71934580-4'
    $content | Set-Content $f.FullName -Encoding UTF8
}
