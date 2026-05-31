import * as React from "react"
import { createPortal } from "react-dom"
import { cn } from "@/lib/utils"

interface AlertDialogProps {
  open?: boolean
  onOpenChange?: (open: boolean) => void
  children?: React.ReactNode
}

const AlertDialogContext = React.createContext<{
  open?: boolean
  onOpenChange?: (open: boolean) => void
}>({})

function AlertDialog({ open, onOpenChange, children }: AlertDialogProps) {
  return (
    <AlertDialogContext.Provider value={{ open, onOpenChange }}>
      {children}
    </AlertDialogContext.Provider>
  )
}

function AlertDialogPortal({ children }: { children: React.ReactNode }) {
  const [mounted, setMounted] = React.useState(false)
  React.useEffect(() => {
    setMounted(true)
    return () => setMounted(false)
  }, [])
  if (!mounted) return null
  return createPortal(children, document.body)
}

function AlertDialogOverlay({ className, ...props }: React.ComponentProps<"div">) {
  const context = React.useContext(AlertDialogContext)
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

function AlertDialogContent({
  className,
  children,
  ...props
}: React.ComponentProps<"div">) {
  const context = React.useContext(AlertDialogContext)
  if (!context.open) return null

  return (
    <AlertDialogPortal>
      <AlertDialogOverlay />
      <div className="fixed inset-0 z-50 flex items-center justify-center p-4">
        <div
          className={cn(
            "relative w-full max-w-md rounded-2xl bg-white p-6 shadow-xl border border-slate-100 flex flex-col gap-4 animate-in fade-in-0 zoom-in-95 duration-200",
            className
          )}
          {...props}
        >
          {children}
        </div>
      </div>
    </AlertDialogPortal>
  )
}

function AlertDialogHeader({ className, ...props }: React.ComponentProps<"div">) {
  return (
    <div
      className={cn(
        "flex flex-col space-y-2 text-center sm:text-left",
        className
      )}
      {...props}
    />
  )
}

function AlertDialogFooter({ className, ...props }: React.ComponentProps<"div">) {
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

function AlertDialogTitle({ className, ...props }: React.ComponentProps<"h2">) {
  return (
    <h2
      className={cn("text-lg font-bold text-slate-900 leading-none tracking-tight", className)}
      {...props}
    />
  )
}

function AlertDialogDescription({
  className,
  ...props
}: React.ComponentProps<"p">) {
  return (
    <p
      className={cn("text-sm text-slate-500 leading-normal", className)}
      {...props}
    />
  )
}

function AlertDialogAction({
  className,
  ...props
}: React.ComponentProps<"button">) {
  return (
    <button
      type="button"
      className={cn(
        "inline-flex h-9 items-center justify-center rounded-lg bg-rose-600 px-4 text-sm font-semibold text-white hover:bg-rose-700 focus:outline-hidden focus:ring-2 focus:ring-rose-500 focus:ring-offset-2 disabled:opacity-50 cursor-pointer",
        className
      )}
      {...props}
    />
  )
}

function AlertDialogCancel({
  className,
  onClick,
  ...props
}: React.ComponentProps<"button">) {
  const context = React.useContext(AlertDialogContext)
  return (
    <button
      type="button"
      onClick={(e) => {
        onClick?.(e)
        context.onOpenChange?.(false)
      }}
      className={cn(
        "inline-flex h-9 items-center justify-center rounded-lg border border-slate-200 bg-white px-4 text-sm font-semibold text-slate-700 hover:bg-slate-50 focus:outline-hidden focus:ring-2 focus:ring-slate-500 focus:ring-offset-2 disabled:opacity-50 cursor-pointer",
        className
      )}
      {...props}
    />
  )
}

export {
  AlertDialog,
  AlertDialogContent,
  AlertDialogHeader,
  AlertDialogFooter,
  AlertDialogTitle,
  AlertDialogDescription,
  AlertDialogAction,
  AlertDialogCancel,
}
