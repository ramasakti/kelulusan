import * as React from "react"
import { createPortal } from "react-dom"
import { X } from "lucide-react"
import { cn } from "@/lib/utils"

interface DialogProps {
  open?: boolean
  onOpenChange?: (open: boolean) => void
  children?: React.ReactNode
}

const DialogContext = React.createContext<{
  open?: boolean
  onOpenChange?: (open: boolean) => void
}>({})

function Dialog({ open, onOpenChange, children }: DialogProps) {
  return (
    <DialogContext.Provider value={{ open, onOpenChange }}>
      {children}
    </DialogContext.Provider>
  )
}

function DialogTrigger({
  children,
  onClick,
  ...props
}: React.HTMLAttributes<HTMLElement> & { children: React.ReactElement<any> }) {
  const context = React.useContext(DialogContext)
  return React.cloneElement(children, {
    onClick: (e: React.MouseEvent<HTMLElement>) => {
      onClick?.(e);
      context.onOpenChange?.(true);
    },
    ...props,
  })
}

function DialogPortal({ children }: { children: React.ReactNode }) {
  const [mounted, setMounted] = React.useState(false)
  React.useEffect(() => {
    setMounted(true)
    return () => setMounted(false)
  }, [])
  if (!mounted) return null
  return createPortal(children, document.body)
}

function DialogOverlay({ className, ...props }: React.ComponentProps<"div">) {
  const context = React.useContext(DialogContext)
  if (!context.open) return null
  return (
    <div
      className={cn(
        "fixed inset-0 z-50 bg-black/60 backdrop-blur-xs transition-all animate-in fade-in-0 duration-200",
        className
      )}
      onClick={() => context.onOpenChange?.(false)}
      {...props}
    />
  )
}

function DialogContent({
  className,
  children,
  ...props
}: React.ComponentProps<"div">) {
  const context = React.useContext(DialogContext)
  if (!context.open) return null

  return (
    <DialogPortal>
      <DialogOverlay />
      <div className="fixed inset-0 z-50 flex items-center justify-center p-4">
        <div
          className={cn(
            "relative w-full max-w-lg rounded-2xl bg-white p-6 shadow-xl border border-slate-100 flex flex-col gap-4 animate-in fade-in-0 zoom-in-95 duration-200",
            className
          )}
          {...props}
        >
          {children}
          <button
            onClick={() => context.onOpenChange?.(false)}
            className="absolute right-4 top-4 rounded-sm opacity-70 ring-offset-background transition-opacity hover:opacity-100 focus:outline-hidden focus:ring-2 focus:ring-ring focus:ring-offset-2 disabled:pointer-events-none cursor-pointer text-slate-400 hover:text-slate-600"
          >
            <X className="h-4 w-4" />
            <span className="sr-only">Close</span>
          </button>
        </div>
      </div>
    </DialogPortal>
  )
}

function DialogHeader({ className, ...props }: React.ComponentProps<"div">) {
  return (
    <div
      className={cn(
        "flex flex-col space-y-1.5 text-center sm:text-left",
        className
      )}
      {...props}
    />
  )
}

function DialogFooter({ className, ...props }: React.ComponentProps<"div">) {
  return (
    <div
      className={cn(
        "flex flex-col-reverse sm:flex-row sm:justify-end sm:space-x-2 gap-2 sm:gap-0",
        className
      )}
      {...props}
    />
  )
}

function DialogTitle({ className, ...props }: React.ComponentProps<"h2">) {
  return (
    <h2
      className={cn(
        "text-lg font-bold text-slate-900 leading-none tracking-tight",
        className
      )}
      {...props}
    />
  )
}

function DialogDescription({
  className,
  ...props
}: React.ComponentProps<"p">) {
  return (
    <p
      className={cn("text-sm text-slate-500", className)}
      {...props}
    />
  )
}

export {
  Dialog,
  DialogTrigger,
  DialogContent,
  DialogHeader,
  DialogFooter,
  DialogTitle,
  DialogDescription,
}
