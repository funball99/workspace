# .bash_profile

# Get the aliases and functions
if [ -f ~/.bashrc ]; then
	. ~/.bashrc
fi

# User specific environment and startup programs

PATH=$PATH:$HOME/bin:/sbin:/usr/bin:/usr/sbin
PATH=~/workSpace/shell/bin:"$PATH"
PATH=/usr/local/bin:"$PATH"

## init
#. /imcoming
## function
function vimbash(){
vim ~/.bash_profile
}
function srcbash(){
source ~/.bash_profile
}
export PATH

#screen -rU

