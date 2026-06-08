import os
import re

directory = '/Users/charlesbeucher/sae203/pages/'
files = [
    'intranet_gestion-clients.php',
    'intranet_gestion-employes.php',
    'intranet_gestion-partenaires.php',
    'intranet_gestion-utilisateurs.php',
    'intranet_fichiers.php'
]

for filename in files:
    path = os.path.join(directory, filename)
    if not os.path.exists(path):
        continue
    
    with open(path, 'r', encoding='utf-8') as f:
        content = f.read()
    
    # 1. Inject verifierCSRFToken() after POST check
    content = re.sub(
        r"(if\s*\(\$_SERVER\['REQUEST_METHOD'\]\s*===\s*'POST'\)\s*\{)",
        r"\1\n    verifierCSRFToken();",
        content
    )
    
    # 2. Inject CSRF hidden input right after <form ... method="POST" ...>
    # or <form method="POST">
    # We use a regex that matches <form ...> containing method="POST"
    # and appends the input inside the form.
    def form_replacer(match):
        form_tag = match.group(0)
        # Avoid double injection
        if 'name="csrf_token"' in form_tag or 'csrf_token' in content[match.end():match.end()+100]:
            return form_tag
        return form_tag + '\n<input type="hidden" name="csrf_token" value="<?= genererCSRFToken() ?>">'
    
    content = re.sub(
        r"<form[^>]*method=[\"']POST[\"'][^>]*>",
        form_replacer,
        content,
        flags=re.IGNORECASE
    )
    
    with open(path, 'w', encoding='utf-8') as f:
        f.write(content)

print("CSRF application complete.")
