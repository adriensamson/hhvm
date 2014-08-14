"""
Hash functions for use with container accessors.
"""
# @lint-avoid-python-3-compatibility-imports

import gdb
from gdbutils import *


#------------------------------------------------------------------------------
# Hash implementations.

def hash_ctca(ctca):
    return ctca.cast(T('uintptr_t'))


#------------------------------------------------------------------------------
# Hash dispatcher.

hashes = {
    'HPHP::JIT::CTCA':  hash_ctca,
}

def hash_of(value):
    t = value.type

    for (htype, hfunc) in hashes.iteritems():
        if t == T(htype):
            return hfunc(value)

    return value