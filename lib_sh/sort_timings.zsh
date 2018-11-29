#!/usr/bin/env zsh

# Takes a list of commands with timing information and lists the commands from
# longest- to shortest-running.
#
# The script reads from the filename given on the command line, or from
# standard input if no filename is given. The input is expected to look like
#
#     +1518804574.3228740692 colors:76> local k
#     +1518804574.3228929043 colors:77> k=44
#     +1518804574.3229091167 colors:77> color[${color[$k]}]=44
#     +1518804574.3229229450 colors:77> k=33
#     +1518804574.3229279518 colors:77> color[${color[$k]}]=33
#
# Everything between the leading "+" and the next space is taken to be a
# decimal number of seconds with at least microsecond precision (i.e., at least
# six digits after the decimal point). Additional digits are allowed but
# ignored. The rest of the line does not need to be in any particular format,
# but the script will truncate this portion of the line with "..." so that it
# is no longer than 80 characters.
#
# The output will look like
#
#     18 colors:76> local k
#     17 colors:77> k=44
#     13 colors:77> color[${color[$k]}]=44
#     5 colors:77> k=33
#
# The first number on each line is the number of microseconds taken by that
# command. (Depending on how you obtain the timing information, the values may
# or may not actually be accurate to the microsecond. This should still give
# you a good idea of the relative timings, though.)
#
# See https://esham.io/2018/02/zsh-profiling for an explanation of the context
# in which this script is intended to be used.
#
# This script was written by Benjamin Esham (https://esham.io) and is released
# under the following terms:
#
# This is free and unencumbered software released into the public domain.
#
# Anyone is free to copy, modify, publish, use, compile, sell, or distribute
# this software, either in source code form or as a compiled binary, for any
# purpose, commercial or non-commercial, and by any means.
#
# In jurisdictions that recognize copyright laws, the author or authors of this
# software dedicate any and all copyright interest in the software to the
# public domain. We make this dedication for the benefit of the public at large
# and to the detriment of our heirs and successors. We intend this dedication
# to be an overt act of relinquishment in perpetuity of all present and future
# rights to this software under copyright law.
#
# THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
# IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
# FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
# AUTHORS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY, WHETHER IN AN
# ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM, OUT OF OR IN CONNECTION
# WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE SOFTWARE.
#
# For more information, please refer to <http://unlicense.org/>


typeset -a lines
typeset -i prev_time=0
typeset prev_command

while read line; do
    # Anything between the beginning of the line and the "+" is just ignored.
    #
    # In particular, in cases where one logging line stops abruptly and a new
    # one begins without an intervening newline, the ".*" in this pattern
    # slurps up the incomplete logging line and leaves only the full line that
    # comes after. This kind of thing can happen if your zshrc contains an
    # assignment like now=$(date); the XTRACE module prints a line
    # corresponding to "now=" but then neglects to print a newline before the
    # line corresponding to the "date" command. The net effect is that the
    # report outputted by this script ignores the "now=" part, which shouldn't
    # have taken any time anyway.
    if [[ $line =~ '^.*\+([0-9]{10})\.([0-9]{6})[0-9]* (.+)' ]]; then
        # Form a time in microseconds by concatenating the digits before the
        # decimal point with the first six digits after the decimal point. 
        integer this_time=$match[1]$match[2]

        if [[ $prev_time -gt 0 ]]; then
            time_difference=$(( $this_time - $prev_time ))
            lines+="$time_difference $prev_command"
        fi

        prev_time=$this_time

        # If the command is longer than 80 characters, truncate it with "...".
        local this_command=$match[3]
        if [[ ${#this_command} -le 80 ]]; then
            prev_command=$this_command
        else
            prev_command="${this_command:0:77}..."
        fi
    fi
done < ${1:-/dev/stdin}

# The combination "On" means that the elements of $lines are sorted numerically
# in descending order. The "-l" flag means that each element is printed on its
# own line.
print -l ${(@On)lines}