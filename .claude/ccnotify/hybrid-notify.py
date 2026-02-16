#!/usr/bin/env python3
# -*- coding: utf-8 -*-
"""
CCNotify Hybride - Combine notifications Windows + Terminal pour WSL
"""

import os
import sys
import subprocess
import time
from datetime import datetime

# Fix Windows console encoding
if sys.platform == 'win32':
    try:
        sys.stdout.reconfigure(encoding='utf-8', errors='replace')
        sys.stderr.reconfigure(encoding='utf-8', errors='replace')
    except AttributeError:
        import io
        sys.stdout = io.TextIOWrapper(sys.stdout.buffer, encoding='utf-8', errors='replace')
        sys.stderr = io.TextIOWrapper(sys.stderr.buffer, encoding='utf-8', errors='replace')

def send_windows_toast(title, message):
    """Envoie une notification toast Windows via WinRT (fonctionne en arrière-plan)"""
    try:
        escaped_title = title.replace('"', '\\"').replace("'", "\\'")
        escaped_message = message.replace('"', '\\"').replace("'", "\\'")
        ps_script = (
            "$t = [Windows.UI.Notifications.ToastNotificationManager, Windows.UI.Notifications, ContentType = WindowsRuntime]"
            "::GetTemplateContent([Windows.UI.Notifications.ToastTemplateType, Windows.UI.Notifications, ContentType = WindowsRuntime]::ToastText02);"
            f"$t.SelectSingleNode('//text[@id=\"1\"]').InnerText = '{escaped_title}';"
            f"$t.SelectSingleNode('//text[@id=\"2\"]').InnerText = '{escaped_message}';"
            "$appId = '{1AC14E77-02E7-4E5D-B744-2EB1AE5198B7}\\WindowsPowerShell\\v1.0\\powershell.exe';"
            "[Windows.UI.Notifications.ToastNotificationManager]::CreateToastNotifier($appId).Show($t)"
        )
        cmd = ['powershell.exe', '-ExecutionPolicy', 'Bypass', '-Command', ps_script]
        subprocess.run(cmd, capture_output=True, text=True, timeout=10)
        return True
    except:
        return False

def send_terminal_notification(title, message, urgency='normal'):
    """Notification colorée dans le terminal"""
    colors = {
        'critical': '\033[41;97m',  # Fond rouge, texte blanc
        'normal': '\033[44;97m',    # Fond bleu, texte blanc  
        'low': '\033[42;30m',       # Fond vert, texte noir
        'reset': '\033[0m'
    }
    
    color = colors.get(urgency, colors['normal'])
    reset = colors['reset']
    width = 60
    
    # Triple bip pour les notifications critiques
    if urgency == 'critical':
        print('\a\a\a', end='', flush=True)
    else:
        print('\a', end='', flush=True)
    
    print(f"\n{color}")
    print("═" * width)
    print(f"  🔔 {title.upper()}")
    print("═" * width)
    
    lines = message.split('\n')
    for line in lines:
        if len(line) > width - 4:
            while line:
                chunk = line[:width-4]
                print(f"  {chunk}")
                line = line[width-4:]
        else:
            print(f"  {line}")
    
    print("═" * width)
    print(f"  {datetime.now().strftime('%H:%M:%S')}")
    print("═" * width)
    print(f"{reset}\n")

def hybrid_notify(title, message, urgency='normal'):
    """Notification hybride : Windows popup + Terminal"""
    
    # 1. Toast Windows (fonctionne en arrière-plan, non-bloquant)
    try:
        import threading
        toast_thread = threading.Thread(target=send_windows_toast, args=(title, message))
        toast_thread.daemon = True
        toast_thread.start()
    except:
        pass
    
    # 2. Notification terminal (immédiate et visible)
    send_terminal_notification(title, message, urgency)

def main():
    if len(sys.argv) < 2:
        print("ok")
        return
    
    action = sys.argv[1]
    project = os.path.basename(os.getcwd())
    
    if action == "UserPromptSubmit":
        hybrid_notify(
            "Claude Started",
            f"New task started\nProject: {project}",
            'normal'
        )
    
    elif action == "Stop":
        hybrid_notify(
            "Task Completed",
            f"Claude has finished the task\nProject: {project}\n\n✅ Check the results!",
            'low'
        )
    
    elif action == "Notification":
        hybrid_notify(
            "Input Required",
            f"Claude is waiting for your response\nProject: {project}\n\n⚠️  PLEASE CHECK CLAUDE INTERFACE ⚠️",
            'critical'
        )
    
    else:
        hybrid_notify(
            "Claude Notification",
            f"Action: {action}\nProject: {project}",
            'normal'
        )

if __name__ == "__main__":
    main()