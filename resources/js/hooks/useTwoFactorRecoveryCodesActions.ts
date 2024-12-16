export function handleTwoFactorAuthenticationActions() {
  const copyKey = (twoFactorAuthenticationSetupCode: string) => {
    if(navigator.clipboard) {
      navigator.clipboard.writeText(twoFactorAuthenticationSetupCode);
      return true;
    } else {
      return false;
    }
  };

  const printRecoveryCodes = (twoFactorAuthenticationRecoveryCodes: string) => {
    const win = window.open()
    win?.document.write(twoFactorAuthenticationRecoveryCodes)
    win?.print()
    win?.close()
  };

  const copyRecoveryCodes = (twoFactorAuthenticationRecoveryCodes: string) => {
    if(navigator.clipboard) {
      navigator.clipboard.writeText(twoFactorAuthenticationRecoveryCodes);
      return true;
    } else {
      return false;
    }
  };

  const downloadRecoveryCodes = (twoFactorAuthenticationRecoveryCodes: string) => {
    let element = document.createElement('a');
    element.setAttribute('href', 'data:text/plain;charset=utf-8,' + encodeURIComponent(twoFactorAuthenticationRecoveryCodes));
    element.setAttribute('download', 'craftable-recovery-codes.txt');
    element.style.display = 'none';

    document.body.appendChild(element);
    element.click();
    document.body.removeChild(element);
  };

  return {
    copyKey,
    printRecoveryCodes,
    copyRecoveryCodes,
    downloadRecoveryCodes
  }
}
